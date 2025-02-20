<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Menu;

class MenuController extends Controller{
    // Get all menus
	public function index(){
		// Retrieve all menus with their submenus and sub-submenus
		$menus = Menu::whereNull('parent_id')->with(['subMenus.subMenus'])->get();
	
		// Process the menus to remove any duplicate submenus and nested submenus
		$menus->each(function ($menu) {
			if ($menu->sub_menus) {
				$menu->sub_menus = $menu->sub_menus->map(function ($subMenu) {
					if ($subMenu->sub_menus) {
						$subMenu->sub_menus = $subMenu->sub_menus->map(function ($subSubMenu) {
							return $subSubMenu;  // Return sub-submenus without redundancy
						});
					}
					return $subMenu;
				});
			}
		});
	
		return response()->json($menus);
	}
	
// 	public function index(Request $request){
//     // Get the authenticated user
//     $user = Auth::guard('api')->user();

//     // Get the role of the authenticated user
//     $roleId = $user->role_id;

//     // Retrieve the menus that the role has permissions for
//     $menus = Menu::whereNull('parent_id')
//         ->with(['subMenus.subMenus' => function ($query) use ($roleId) {
//             $query->whereHas('rolePermissions', function ($q) use ($roleId) {
//                 $q->where('role_id', $roleId);
//             });
//         }])
//         ->whereHas('rolePermissions', function ($query) use ($roleId) {
//             $query->where('role_id', $roleId);
//         })
//         ->get();

//     return response()->json($menus);
// }


    // Create a new menu
	public function store(Request $request){
		// Validate input
		$request->validate([
			'name' => 'required|string',
			'type' => 'required|in:main,sub,sub-sub',
			'parent_id' => 'nullable|exists:menus,id', // Required for sub and sub-sub menus
		]);
	
		// Check for duplicate menu name
		$existingMenu = Menu::where('name', $request->name)
							->where('parent_id', $request->parent_id)
							->first(); // Look for a menu with the same name and same parent_id (if it's a sub or sub-sub)
	
		if ($existingMenu) {
			return response()->json([
				'message' => 'A menu with this name already exists under the selected parent.',
			], 409); // 409 Conflict
		}
	
		// If no duplicates, create the menu
		$menu = new Menu();
		$menu->name = $request->name;
		$menu->type = $request->type;
		$menu->parent_id = $request->parent_id;
		$menu->save();
	
		return response()->json($menu, 201); // Return the newly created menu
	}
	
    // Show menu details
    public function show($id){
        $menu = Menu::with('subMenus.subMenus')->find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }
        return response()->json($menu);
    }

    // Update a menu
    public function update(Request $request, $id){
        $request->validate([
            'name' => 'nullable|string',
            'type' => 'nullable|in:main,sub,sub-sub',
            'parent_id' => 'nullable|exists:menus,id', // Parent menu required for sub and sub-sub
        ]);

        $menu = Menu::find($id);
        if (!$menu) {
            return response()->json(['message' => 'Menu not found'], 404);
        }

        if ($request->name) {
            $menu->name = $request->name;
        }

        if ($request->type) {
            $menu->type = $request->type;
        }

        if ($request->parent_id) {
            $menu->parent_id = $request->parent_id;
        }

        $menu->save();

        return response()->json($menu);
    }

    // Delete a menu
	public function destroy($id)
	{
		// Find the menu by its ID
		$menu = Menu::find($id);
	
		// Check if the menu exists
		if (!$menu) {
			return response()->json(['message' => 'Menu not found'], 404);
		}
	
		// Check if the menu has submenus and delete them recursively
		if ($menu->subMenus) {
			foreach ($menu->subMenus as $subMenu) {
				$subMenu->deleteSubMenus(); // Recursively delete submenus
			}
		}
	
		// Delete the menu itself
		$menu->delete();
	
		return response()->json(['message' => 'Menu deleted successfully']);
	}
	
	// Add this method in the Menu model to handle recursive deletion of submenus
	public function deleteSubMenus(){
		// Delete sub-submenus first if any
		if ($this->subMenus) {
			foreach ($this->subMenus as $subSubMenu) {
				$subSubMenu->deleteSubMenus(); // Recursively delete sub-submenus
			}
		}
	
		// Delete the current submenu (this could be sub-submenu as well)
		$this->delete();
	}
	
}

