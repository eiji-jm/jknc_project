namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ubo extends Model
{
    use HasFactory;

    protected $fillable = [
        'complete_name',
        'address',
        'nationality',
        'date_of_birth',
        'tax_identification_no',
        'ownership_percentage',
        'ownership_type',
        'category'
    ];
}
