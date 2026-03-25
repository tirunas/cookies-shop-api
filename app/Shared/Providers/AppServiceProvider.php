<?php

namespace App\Shared\Providers;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->registerDomainMigrations();
        $this->registerDomainConfigs();
        $this->registerDomainFactories();
    }

    public function boot(): void
    {
        $this->registerDomainRoutes();
        $this->registerDomainSeeders();
    }

    private function getDomainPaths(): array
    {
        $domainPath = app_path('Domain');

        if (!is_dir($domainPath)) {
            return [];
        }

        $paths = [];

        foreach (File::directories($domainPath) as $domainDir) {
            foreach (File::directories($domainDir) as $versionDir) {
                $paths[] = $versionDir;
            }
        }

        return $paths;
    }

    private function registerDomainRoutes(): void
    {
        foreach ($this->getDomainPaths() as $versionPath) {
            $routesPath = $versionPath . '/Routes';

            if (!is_dir($routesPath)) {
                continue;
            }

            foreach (File::files($routesPath) as $routeFile) {
                $prefix = $this->resolveRoutePrefix($versionPath);

                match ($routeFile->getFilenameWithoutExtension()) {
                    'api' => Route::prefix("api/{$prefix}")->group($routeFile->getPathname()),
                    'web' => Route::prefix($prefix)->group($routeFile->getPathname()),
                    default => Route::prefix("api/{$prefix}")->group($routeFile->getPathname()),
                };
            }
        }
    }

    private function registerDomainMigrations(): void
    {
        foreach ($this->getDomainPaths() as $versionPath) {
            $migrationsPath = $versionPath . '/Database/Migrations';

            if (is_dir($migrationsPath)) {
                $this->loadMigrationsFrom($migrationsPath);
            }
        }
    }

    private function registerDomainConfigs(): void
    {
        foreach ($this->getDomainPaths() as $versionPath) {
            $configPath = $versionPath . '/Config';

            if (!is_dir($configPath)) {
                continue;
            }

            foreach (File::files($configPath) as $configFile) {
                $this->mergeConfigFrom(
                    $configFile->getPathname(),
                    $configFile->getFilenameWithoutExtension(),
                );
            }
        }
    }

    private function registerDomainFactories(): void
    {
        Factory::guessFactoryNamesUsing(function (string $modelName) {
            $namespace = str($modelName)
                ->beforeLast('\\Models\\')
                ->append('\\Database\\Factories\\');

            $factoryClass = str($modelName)
                ->afterLast('\\')
                ->append('Factory');

            return $namespace . $factoryClass;
        });
    }

    private function registerDomainSeeders(): void
    {
        foreach ($this->getDomainPaths() as $versionPath) {
            $seedersPath = $versionPath . '/Database/Seeders';

            if (is_dir($seedersPath)) {
                $this->app->afterResolving('seeder', function ($seeder) use ($seedersPath) {
                    foreach (File::files($seedersPath) as $file) {
                        $seeder->call($file->getFilenameWithoutExtension());
                    }
                });
            }
        }
    }

    private function resolveRoutePrefix(string $versionPath): string
    {
        $parts = explode(DIRECTORY_SEPARATOR, $versionPath);
        $version = end($parts);
        $domain = prev($parts);

        return strtolower("{$version}/{$domain}");
    }
}
