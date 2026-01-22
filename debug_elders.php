
require __DIR__ . '/vendor/autoload.php';
$app = require __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Leader;
use App\Models\User;

$leaders = Leader::where('position', 'elder')->get();

echo "Found " . $leaders->count() . " elders.\n";

foreach ($leaders as $leader) {
    echo "Elder ID: " . $leader->id . "\n";
    echo "  Member ID: " . $leader->member_id . "\n";
    echo "  Name: " . ($leader->member ? $leader->member->full_name : 'No Member') . "\n";
    echo "  Is Active: " . ($leader->is_active ? 'Yes' : 'No') . "\n";
    echo "  End Date: " . $leader->end_date . "\n";
    
    if ($leader->member_id) {
        $user = User::where('member_id', $leader->member_id)->first();
        if ($user) {
            echo "  Has User Account: Yes (Role: " . $user->role . ")\n";
        } else {
            echo "  Has User Account: No\n";
        }
    }
    echo "-------------------\n";
}
