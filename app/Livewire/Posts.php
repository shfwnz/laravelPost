<?php
  
namespace App\Livewire;
  
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Post;
  
class Posts extends Component
{
    public $title, $body, $post_id, $search;
    public $isOpen = 0;
    public $error = '';

    use WithPagination;
    protected $updatesQueryString = ['search'];
  
    public function render()
    {
        $posts = Post::paginate(5);

        if (!empty($this->search)) {
            $posts = Post::where('title', 'like', '%' . $this->search . '%')
                            ->latest()
                            ->paginate(5);
        }

        return view('livewire.posts', [
            'posts' => $posts,
        ]);
    }

    // public function render()
    // {
    //     $posts = Post::paginate(10);
    //     return view('livewire.posts', ['posts' => $posts]);
    // }

    public function search(){
        try {
            $post = Post::where('name', 'like', '%'.$this->search.'%')->first();
            $this->reset(['error']); // set $error to default i.e. ''
        } catch(ModelNotFoundException $e) {
            $this->error = 'Product not found.';
        }
    }
  
    public function create()
    {
        $this->resetInputFields();
        $this->openModal();
    }
  
    public function openModal()
    {
        $this->isOpen = true;
    }
  
    public function closeModal()
    {
        $this->isOpen = false;
    }
  
    private function resetInputFields(){
        $this->title = '';
        $this->body = '';
        $this->post_id = '';
    }
     
    public function store()
    {
        $this->validate([
            'title' => 'required',
            'body' => 'required',
        ]);
   
        Post::updateOrCreate(['id' => $this->post_id], [
            'title' => $this->title,
            'body' => $this->body
        ]);
  
        session()->flash('message', 
            $this->post_id ? 'Post Updated Successfully.' : 'Post Created Successfully.');
  
        $this->closeModal();
        $this->resetInputFields();
    }
  
    public function edit($id)
    {
        $post = Post::findOrFail($id);
        $this->post_id = $id;
        $this->title = $post->title;
        $this->body = $post->body;
    
        $this->openModal();
    }
     
    public function delete($id)
    {
        Post::find($id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }
}
