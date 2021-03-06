<?php

/**
 * This file is part of Laravel Zero.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace LaravelZero\Framework\Commands\App;

use LaravelZero\Framework\Components;
use LaravelZero\Framework\Commands\Command;
use PhpSchool\CliMenu\Terminal\TerminalFactory;
use Symfony\Component\Console\Input\ArrayInput;

/**
 * This is the Laravel Zero Framework Installer Command implementation.
 */
class Installer extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $signature = 'app:install {component? : The component name}';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Installs a new component';

    /**
     * The list of components installers.
     *
     * @var array
     */
    protected $components = [
        'console-dusk' => Components\ConsoleDusk\Installer::class,
        'database' => Components\Database\Installer::class,
        'dotenv' => Components\Dotenv\Installer::class,
        'log' => Components\Log\Installer::class,
    ];

    /**
     * {@inheritdoc}
     */
    public function handle(): void
    {
        $title = 'Laravel Zero - Component installer';

        $choices = [
            'console-dusk' =>   'Console Dusk - Laravel Dusk on artisan commands',
            'database' =>       'Database     - Laravel Eloquent',
            'dotenv' =>         'Dotenv       - Loads environment variables from `.env`',
            'log' =>            'Log          - Laravel Log component',
        ];

        if (! TerminalFactory::fromSystem()->isTTY()) {
            $option = $this->choice($title, $choices);
        } else {
            $option = $this->argument('component') ?: $this->menu(
                $title,
                $choices
            )->setForegroundColour('green')
                ->setBackgroundColour('black')
                ->open();
        }

        if ($option !== null && ! empty($this->components[$option])) {
            ($command = $this->app[$this->components[$option]])->setLaravel($this->app);

            $this->info("Installing {$option} component...");

            $command->run(new ArrayInput([]), $this->output);
        }
    }
}
