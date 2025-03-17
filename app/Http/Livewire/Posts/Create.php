<?php

namespace App\Http\Livewire\Posts;

use App\Models\Media;
use App\Models\Post;
use Livewire\Component;
use Livewire\WithFileUploads;
use Stevebauman\Location\Facades\Location;

class Create extends Component
{
    use WithFileUploads;

    public $title;
    public $body;
    public $file;
    public $location;

    public $imageFormats = ['jpg', 'png', 'gif', 'jpeg'];
    public $videoFormats = ['mp4', '3gp'];

    public function mount()
    {
        $ipAddress = $this->getIp();
        $position = Location::get($ipAddress);

        // If location is found, set it, otherwise set as null
        $this->location = $position ? $position->cityName . '/' . $position->regionCode : null;
    }

    // When file is updated, validate the file type and size
    public function updatedFile()
    {
        $this->validate([
            'file' => 'nullable|mimes:' . implode(',', array_merge($this->imageFormats, $this->videoFormats)) . '|max:2048',
        ]);
    }

    // Submit method to create a new post and store associated files
    public function submit()
    {
        // Validate input fields
        $data = $this->validate([
            'title' => 'required|max:50',
            'location' => 'nullable|string|max:60',
            'body' => 'required|max:1000',
            'file' => 'nullable|mimes:' . implode(',', array_merge($this->imageFormats, $this->videoFormats)) . '|max:2048',
        ]);

        // Create the post in the database
        $post = Post::create([
            'user_id' => auth()->id(),
            'title' => $data['title'],
            'location' => $data['location'],
            'body' => $data['body'],
        ]);

        // Store media files associated with the post
        $this->storeFiles($post);

        // Flash success message
        session()->flash('success', 'Post created successfully');

        // Redirect to home page
        return redirect()->route('home');
    }

    // Render the view
    public function render()
    {
        return view('livewire.posts.create');
    }

    // Method to handle storing of media files
    private function storeFiles($post)
    {
        if (empty($this->file)) {
            return true;  // No file provided
        }

        // Store the file in the public storage
        $path = $this->file->store('post-photos', 'public');

        // Determine if the file is an image (based on extension)
        $isImage = preg_match('/^.*\.(png|jpg|gif)$/i', $path);

        // Store media record in the database
        Media::create([
            'post_id' => $post->id,
            'path' => $path,
            'is_image' => $isImage,
        ]);
    }

    // Method to get the IP address of the client
    private function getIp(): ?string
    {
        foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR'] as $key) {
            if (array_key_exists($key, $_SERVER)) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip);  // sanitize the IP
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip();  // Fallback if no client IP is found
    }
}
