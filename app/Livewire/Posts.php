<?php
  
namespace App\Livewire;
  
use Livewire\WithPagination;
use Livewire\Component;
use App\Models\Post;
  
class Posts extends Component
{
    public $title, $body, $post_id;
    // public $search = '';
    public $isOpen = 0;

    use WithPagination;
  
    public function render()
    {
        $posts = Post::paginate(10);
        return view('livewire.posts', ['posts' => $posts]);
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
