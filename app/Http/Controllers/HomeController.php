<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;
use DataTables;
use Validator;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $categories = Category::get();
        return view('home',compact('categories'));
    }
    
    public function categorylist(Request $request){
        
        $data = Category::orderby('id', 'desc')->get();
        return DataTables::of($data)
            ->addColumn('action', function ($q) {
                return '<a href="javascript:;" data-id="' . $q->id . '" class="update_record btn btn-primary"><i class="fa fa-pencil-alt"></i></span>
                        </a> | 
                        <a href="javascript:;" data-id="' . $q->id . '" class="delete_record btn btn-danger"><i class="fa fa-trash-alt"></i></span>
                        </a>'; 
            })
            ->addColumn('name', function ($q) {
                return $q->name;
            })
            ->addColumn('parent_name', function ($q) {
                if(!empty($q->parent)){
                    return $q->parent->name;
                }else{
                    return '';
                }
            })
            ->addIndexColumn()
            ->filter(function ($instance) use ($request) {
                $instance->collection = $instance->collection->filter(function ($row) use ($request) {
                    $search = $request->search['value'];
                    $name = preg_match("/{$search}/i", $row['name']);
                    $parent_name = preg_match("/{$search}/i", $row['parent_name']);
                    return $name || $parent_name;
                });
            })
            ->rawColumns(['action'])->make(true);
    }
    
    // Insert/update category data
    public function storecategory(Request $request) {
        $input = $request->all();
        $rules = [
            'name' => ['required', 'string', 'max:50']
        ];
        $messages = [
            'name.required' => 'Please enter name.',
            'name.max' => 'Name must be less than 50 character'
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            $arr = array("status" => 400, "msg" => $validator->errors()->first(), "result" => array());
        } else {
            try {
                if(!empty($input['id'])){
                    $store = Category::find($input['id']);
                    $msg = 'Updated successfully';
                }else{
                    $store = new Category;
                    $msg = 'Added successfully';
                }
                $store->name = $input['name'];
                $store->parent_id = $input['parent_id'] ?? 0;
                $store->save();
                
                $arr = array("status" => 200, "msg" =>$msg, "result" => array('data'=>$store));
            } catch (\Illuminate\Database\QueryException $ex) {
                $msg = $ex->getMessage();
                if (isset($ex->errorInfo[2])) :
                    $msg = $ex->errorInfo[2];
                endif;

                $arr = array("status" => 400, "msg" => $msg, "result" => array());
            } catch (Exception $ex) {
                $msg = $ex->getMessage();
                if (isset($ex->errorInfo[2])) :
                    $msg = $ex->errorInfo[2];
                endif;

                $arr = array("status" => 400, "msg" => $msg, "result" => array());
            }
        }
        return $arr;
    }
    
    // Get category detail by id
    public function categorydetail(Request $request) {
        try {
            $input = $request->all();
            if (!empty($input['id'])) {
                $data = Category::find($input['id']);
                if(!empty($data)){
                    $arr = array("status" => 200, "msg" => 'success', "result" => array('data'=>$data));
                }else{
                    $arr = array("status" => 400, "msg" => 'success', "No data found" => array());
                }
            } else {
                $arr = array("status" => 400, "msg" => 'success', "No data found" => array());
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = $ex->getMessage();
            if (isset($ex->errorInfo[2])) :
                $msg = $ex->errorInfo[2];
            endif;

            $arr = array("status" => 400, "msg" => $msg, "result" => array());
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            if (isset($ex->errorInfo[2])) :
                $msg = $ex->errorInfo[2];
            endif;

            $arr = array("status" => 400, "msg" => $msg, "result" => array());
        }
        return $arr;
    }
    
    // Delete category
    public function categorydelete(Request $request){
        try {
            $input = $request->all();
            if (!empty($input['id'])) {
                $data = Category::find($input['id']);
                if(!empty($data)){
                    $count = Category::where('parent_id',$input['id'])->count();
                    if($count > 0){
                        $arr = array("status" => 400, "msg" => 'This category consists of another category so you can not delete it.', "result" => array());
                    }else{
                        Category::where('id',$input['id'])->delete();
                        $arr = array("status" => 200, "msg" => 'success', "result" => array());
                    }
                }else{
                    $arr = array("status" => 400, "msg" => 'error', "No data found" => array());
                }
            } else {
                $arr = array("status" => 400, "msg" => 'error', "No data found" => array());
            }
        } catch (\Illuminate\Database\QueryException $ex) {
            $msg = $ex->getMessage();
            if (isset($ex->errorInfo[2])) :
                $msg = $ex->errorInfo[2];
            endif;

            $arr = array("status" => 400, "msg" => $msg, "result" => array());
        } catch (Exception $ex) {
            $msg = $ex->getMessage();
            if (isset($ex->errorInfo[2])) :
                $msg = $ex->errorInfo[2];
            endif;

            $arr = array("status" => 400, "msg" => $msg, "result" => array());
        }
        return $arr;
    }
    
    //Load tree view of category
    public function loadtree(Request $request){
        $categories = Category::where('parent_id','0')->get();
        if(!empty($categories)){
            $html = '';
            foreach($categories as $category){
                $html.='<li>'.$category->name;
                    if(count($category->childs)){
                        $html.= view('showchild', ['childs' => $category->childs])->render();
                    }
                $html.='</li>';
            }
            $arr = array("status" => 200, "msg" => 'success', "result" => array('html'=>$html));
        }else{
            $arr = array("status" => 400, "msg" => 'error', "result" => array());
        }
        return $arr;
    }

}
