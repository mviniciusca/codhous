<?php

namespace App\Livewire;

use Livewire\Component;

class Darkmode extends Component
{
    public string $active;
    public string $theme;
    public function mount()
    {
        $this->active = isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark';
        $this->theme = $this->active ? 'dark' : 'light';
    }

    public function toggleDarkMode()
    {
        if ($this->active) {
            setcookie('theme', 'dark', time() + (86400 * 30), "/");
            session(['theme' => 'dark']);
            $this->js("document.documentElement.classList.add('dark')");
            $this->theme = 'light';
        } else {
            setcookie('theme', 'light', time() + (86400 * 30), "/");
            session(['theme' => 'light']);
            $this->js("document.documentElement.classList.remove('dark')");
            $this->theme = 'dark';
        }
    }
    public function render()
    {
        return view('livewire.darkmode', ['darkMode' => $this->toggleDarkMode()]);
    }
}
