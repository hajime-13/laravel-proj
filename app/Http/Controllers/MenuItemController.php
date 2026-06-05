<?php

namespace App\Http\Controllers;

use App\Models\MenuItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MenuItemController extends Controller
{
    public function index()
    {
        $menuItems = MenuItem::where('user_id', Auth::id())->latest()->paginate(10);
        return view('menu.index', compact('menuItems'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
            'available'   => 'nullable',
        ]);

        $data = [
            'user_id'     => Auth::id(),
            'name'        => $request->name,
            'category'    => $request->category,
            'price'       => $request->price,
            'description' => $request->description,
            'available'   => $request->has('available'),
        ];

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        MenuItem::create($data);

        return redirect()->route('menu.index')
            ->with('toast_success', 'Menu item "' . $request->name . '" added successfully.');
    }

    public function edit(MenuItem $menu)
    {
        $this->authorizeMenuItem($menu);
        return view('menu.edit', compact('menu'));
    }

    public function update(Request $request, MenuItem $menu)
    {
        $this->authorizeMenuItem($menu);

        $request->validate([
            'name'        => 'required|string|max:255',
            'category'    => 'required|string|max:100',
            'price'       => 'required|numeric|min:0',
            'description' => 'nullable|string|max:500',
            'image'       => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = [
            'name'        => $request->name,
            'category'    => $request->category,
            'price'       => $request->price,
            'description' => $request->description,
            'available'   => $request->has('available'),
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($menu->image) {
                Storage::disk('public')->delete($menu->image);
            }
            $data['image'] = $request->file('image')->store('menu', 'public');
        }

        // Remove image if checkbox checked
        if ($request->has('remove_image') && $menu->image) {
            Storage::disk('public')->delete($menu->image);
            $data['image'] = null;
        }

        $menu->update($data);

        return redirect()->route('menu.index')
            ->with('toast_success', 'Menu item "' . $menu->name . '" updated successfully.');
    }

    public function destroy(MenuItem $menu)
    {
        $this->authorizeMenuItem($menu);

        // Delete image file if exists
        if ($menu->image) {
            Storage::disk('public')->delete($menu->image);
        }

        $name = $menu->name;
        $menu->delete();

        return redirect()->route('menu.index')
            ->with('toast_danger', 'Menu item "' . $name . '" deleted.');
    }

    private function authorizeMenuItem(MenuItem $menu): void
    {
        if ($menu->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
