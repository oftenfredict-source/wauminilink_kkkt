<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class UpdateAllViewsForTranslation extends Command
{
    protected $signature = 'translate:all-views {--dry-run : Show what would be changed}';
    protected $description = 'Update all view files to use autoTranslate() for common English text';

    protected $commonPatterns = [
        // Headers and titles
        '>Overview<' => '>{{ autoTranslate(\'Overview\') }}<',
        '>Dashboard<' => '>{{ autoTranslate(\'Dashboard\') }}<',
        '>Settings<' => '>{{ autoTranslate(\'Settings\') }}<',
        '>Login<' => '>{{ autoTranslate(\'Login\') }}<',
        '>Logout<' => '>{{ autoTranslate(\'Logout\') }}<',
        
        // Common actions
        '>Save<' => '>{{ autoTranslate(\'Save\') }}<',
        '>Cancel<' => '>{{ autoTranslate(\'Cancel\') }}<',
        '>Delete<' => '>{{ autoTranslate(\'Delete\') }}<',
        '>Edit<' => '>{{ autoTranslate(\'Edit\') }}<',
        '>View<' => '>{{ autoTranslate(\'View\') }}<',
        '>Create<' => '>{{ autoTranslate(\'Create\') }}<',
        '>Update<' => '>{{ autoTranslate(\'Update\') }}<',
        '>Add<' => '>{{ autoTranslate(\'Add\') }}<',
        '>Search<' => '>{{ autoTranslate(\'Search\') }}<',
        '>Filter<' => '>{{ autoTranslate(\'Filter\') }}<',
        
        // Common labels
        '>Name<' => '>{{ autoTranslate(\'Name\') }}<',
        '>Email<' => '>{{ autoTranslate(\'Email\') }}<',
        '>Password<' => '>{{ autoTranslate(\'Password\') }}<',
        '>Phone<' => '>{{ autoTranslate(\'Phone\') }}<',
        '>Address<' => '>{{ autoTranslate(\'Address\') }}<',
        '>Actions<' => '>{{ autoTranslate(\'Actions\') }}<',
        
        // Messages
        '>No data available<' => '>{{ autoTranslate(\'No data available\') }}<',
        '>Loading...<' => '>{{ autoTranslate(\'Loading...\') }}<',
        '>Welcome<' => '>{{ autoTranslate(\'Welcome\') }}<',
    ];

    public function handle()
    {
        $viewsPath = resource_path('views');
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No files will be modified');
        }

        $this->info('Scanning and updating all view files...');
        
        $files = File::allFiles($viewsPath);
        $updated = 0;
        $skipped = ['language-switcher.blade.php']; // Already translated
        
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            
            $filename = $file->getFilename();
            if (in_array($filename, $skipped)) {
                continue;
            }
            
            $content = File::get($file->getPathname());
            $originalContent = $content;
            
            // Apply common patterns
            foreach ($this->commonPatterns as $pattern => $replacement) {
                // Only replace if not already wrapped
                if (strpos($content, $pattern) !== false && 
                    strpos($content, 'autoTranslate') === false) {
                    $content = str_replace($pattern, $replacement, $content);
                }
            }
            
            if ($content !== $originalContent) {
                $updated++;
                $relativePath = str_replace(base_path() . DIRECTORY_SEPARATOR, '', $file->getPathname());
                
                if ($dryRun) {
                    $this->line("Would update: {$relativePath}");
                } else {
                    File::put($file->getPathname(), $content);
                    $this->info("Updated: {$relativePath}");
                }
            }
        }
        
        if ($updated > 0) {
            if ($dryRun) {
                $this->info("\nWould update {$updated} file(s). Run without --dry-run to apply changes.");
            } else {
                $this->info("\nSuccessfully updated {$updated} file(s).");
                $this->info("Please review the changes and test your application.");
            }
        } else {
            $this->info('No files needed updating.');
        }
    }
}










