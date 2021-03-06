<?php namespace CodeCommerce\Http\Controllers;

use CodeCommerce\Http\Requests;
use CodeCommerce\Http\Controllers\Controller;

use CodeCommerce\Product;
use CodeCommerce\ProductImage;
use CodeCommerce\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;


class AdminProductsController extends Controller
{
    private $productModel;
    public function __construct(Product $product)
    {
        $this->productModel = $product;
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        $produtos = $this->productModel->paginate(10);
		return view('products.list' , compact('produtos'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create(Tag $tagModel)
	{
        $tags = $tagModel->all()->lists('name');
		return view("products.create" , compact('tags'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Requests\ProductRequest $request , Product $model , Tag $tagModel)
	{
        $product = $model->create($request->all());
        $product->tags()->sync($this->getTagIdsByList($request->tags , $tagModel));
		return redirect()->route('products.list');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id , Product $model , Tag $tagModel)
	{
        $product = $model->find($id);
        $tags = $tagModel->all()->lists('name');
		return view('products.edit' , compact('product' , 'tags'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id , Requests\ProductUpdateRequest $request , Product $model , Tag $tagModel)
    {
        $product = $model->find($id);
        $product->update($request->all());
        $product->tags()->sync($this->getTagIdsByList($request->tags , $tagModel));
        return redirect()->route('products.list');
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id , Product $model)
	{
        $produto = $model->find($id);
        foreach($produto->images()->get() as $imgDel)
        {
            file_exists(public_path().'/images/products/'.$imgDel->idExtension) && Storage::disk('productImages')->delete($imgDel->idExtension);
        }
        $produto->delete();
        return redirect()->route('products.list');
	}
    public function images($id , Product $product)
    {
        $product = $product->find($id);
        return view('products.images' , compact('product'));
    }
    public function imageStore($id , Request $request , ProductImage $productImage)
    {
        $file = $request->file('image');
        $ext = $file->getClientOriginalExtension();
        $img = $productImage->create(['product_id' => $id , 'extension' => $ext]);
        Storage::disk('productImages')->put($img->idExtension , File::get($file));
        return redirect()->route('products.images' , ['id' => $id]);
    }

    public function destroyImage($id , ProductImage $productImage)
    {
        $img = $productImage->find($id);
        $productId = $img->product->id;
        if(file_exists(public_path().'/images/products/'.$img->idExtension))
        {
            Storage::disk('productImages')->delete($img->idExtension);
        }
        $img->delete();
        return redirect()->route('products.images' , ['id' => $productId]);
    }

    /**
     * @param string $tagsString
     * @param Tag $model
     * @return array
     */
    private function getTagIdsByList($tagsString , Tag $model)
    {

        // Separando por virgula:
        $tags = explode(',' , $tagsString);

        // É uma lista , ou uma unica entrada? ou uma string vazia?
        $tags = (count($tags) > 1) ? $tags: [$tagsString];

        // criando um array vazio para o loop
        $tagsIds = [];

        // Loop
        foreach($tags as $tag)
        {
            if(!empty($tag))
            {
                $tagDb = $model->firstOrCreate(['name' => $tag]);
                $tagsIds[] = $tagDb->id;
            }
        }
        return $tagsIds;
    }
}
