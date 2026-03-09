namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ubo;

class UboController extends Controller
{

    public function index(Request $request)
    {
        $search = $request->search;

        $ubos = Ubo::when($search, function ($query) use ($search) {
            $query->where('complete_name','like',"%$search%");
        })->paginate(10);

        return view('ubo.index', compact('ubos'));
    }


    public function store(Request $request)
    {
        $request->validate([
            'complete_name'=>'required',
            'address'=>'required',
            'nationality'=>'required'
        ]);

        Ubo::create($request->all());

        return redirect()->route('ubo.index');
    }


    public function edit($id)
    {
        $ubo = Ubo::findOrFail($id);
        return view('ubo.edit',compact('ubo'));
    }


    public function update(Request $request,$id)
    {
        $ubo = Ubo::findOrFail($id);
        $ubo->update($request->all());

        return redirect()->route('ubo.index');
    }


    public function destroy($id)
    {
        Ubo::destroy($id);
        return redirect()->route('ubo.index');
    }
}
