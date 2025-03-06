<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Breadcrumb extends Model
{
    protected $fillable = ['route_name', 'title', 'label', 'parent_id'];

    public function parent()
    {
        return $this->belongsTo(Breadcrumb::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Breadcrumb::class, 'parent_id');
    }

    public function getBreadcrumbPath()
    {
        $breadcrumbs = [];
        $breadcrumb = $this;

        while ($breadcrumb) {
            $breadcrumbs[] = [
                'label' => $breadcrumb->label,
                'title' => $breadcrumb->title,
                'route_name' => $breadcrumb->route_name,
            ];
            $breadcrumb = $breadcrumb->parent; // Assuming you have a parent relation
        }

        return array_reverse($breadcrumbs);
    }

}
