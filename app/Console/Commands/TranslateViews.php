<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class TranslateViews extends Command
{
    protected $signature = 'translate:views {--dry-run : Show what would be changed without making changes}';
    protected $description = 'Automatically wrap English text in views with autoTranslate()';

    protected $commonTexts = [
        'Dashboard' => 'Dashboard',
        'Welcome' => 'Welcome',
        'Save' => 'Save',
        'Cancel' => 'Cancel',
        'Delete' => 'Delete',
        'Edit' => 'Edit',
        'View' => 'View',
        'Create' => 'Create',
        'Update' => 'Update',
        'Search' => 'Search',
        'Filter' => 'Filter',
        'Export' => 'Export',
        'Import' => 'Import',
        'Name' => 'Name',
        'Email' => 'Email',
        'Phone' => 'Phone',
        'Address' => 'Address',
        'Actions' => 'Actions',
        'Settings' => 'Settings',
        'Logout' => 'Logout',
        'Login' => 'Login',
        'Members' => 'Members',
        'Add Member' => 'Add Member',
        'Leadership' => 'Leadership',
        'Campuses' => 'Campuses',
        'Announcements' => 'Announcements',
        'Reports' => 'Reports',
        'Analytics' => 'Analytics',
        'Finance' => 'Finance',
        'Tithes' => 'Tithes',
        'Offerings' => 'Offerings',
        'Donations' => 'Donations',
        'Expenses' => 'Expenses',
        'Change Password' => 'Change Password',
        'My Information' => 'My Information',
        'My Finance' => 'My Finance',
        'Leaders' => 'Leaders',
    ];

    public function handle()
    {
        $viewsPath = resource_path('views');
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('DRY RUN MODE - No files will be modified');
        }

        $this->info('Scanning view files for English text...');
        
        $files = File::allFiles($viewsPath);
        $updated = 0;
        
        foreach ($files as $file) {
            if ($file->getExtension() !== 'php') {
                continue;
            }
            
            $content = File::get($file->getPathname());
            $originalContent = $content;
            
            // Simple pattern matching for common text
            foreach ($this->commonTexts as $text => $translation) {
                // Match text that's not already wrapped in autoTranslate
                $pattern = '/(?<!autoTranslate\([\'"])>(' . preg_quote($text, '/') . ')(?<!\))(?![\'"]\))/';
                
                // Only replace if it's standalone text (not in a function call)
                if (preg_match('/>' . preg_quote($text, '/') . '</', $content) && 
                    !preg_match('/autoTranslate\([\'"]' . preg_quote($text, '/') . '[\'"]\)/', $content)) {
                    $replacement = '>{{ autoTranslate(\'' . $text . '\') }}<';
                    $content = preg_replace('/>' . preg_quote($text, '/') . '</', $replacement, $content);
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
            }
        } else {
            $this->info('No files needed updating.');
        }
    }
}










