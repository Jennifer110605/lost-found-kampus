<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, Item $item)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:comments,id',
        ], [
            'content.required' => 'Komentar tidak boleh kosong.',
            'content.max' => 'Komentar maksimal 500 karakter.',
            'parent_id.exists' => 'Komentar yang direply tidak ditemukan.',
        ]);

        $parentId = $request->input('parent_id');
        if ($parentId) {
            $parentComment = Comment::find($parentId);
            if (!$parentComment || $parentComment->item_id !== $item->id) {
                return redirect()->route('items.show', $item)
                    ->withErrors(['content' => 'Komentar yang direply tidak valid.']);
            }
        }

        Comment::create([
            'user_id' => Auth::id(),
            'item_id' => $item->id,
            'parent_id' => $parentId,
            'content' => $request->input('content'),
        ]);

        return redirect()->route('items.show', $item)
            ->with('success', 'Komentar berhasil ditambahkan!');
    }

    public function destroy(Comment $comment)
    {
        if (Auth::id() !== $comment->user_id) {
            abort(403);
        }

        $item = $comment->item;
        $comment->delete();

        return redirect()->route('items.show', $item)
            ->with('success', 'Komentar berhasil dihapus.');
    }
}
