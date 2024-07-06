<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Exception;
use DB;

use App\Models\Book;
use App\Models\BookIndex;
use App\Http\Requests\StoreBookRequest;

class BookController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {
        $books = Book::all();

        return response()->json([
            'status' => true,
            'books' => $books
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBookRequest $request) {
        DB::beginTransaction();
        try {
            $bookTitle = $request->input('title');

            $book = Book::create([
                'title' => $bookTitle,
                'author_id' => Auth::user()->id
            ]);

            $this->createBookIndices($book->id, $request->input('indices'));

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Book successfully created!',
                'book' => $book
            ], 200);
        } catch (Exception $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], 500);
        }
    }

    private function createBookIndices(int $bookId, array $bookIndices, int $bookIndexId = null) {
        foreach ($bookIndices as $bookIndex) {
            $subindices = $bookIndex['subindices'];
            unset($bookIndex['subindices']);

            $newBookIndex = BookIndex::create([
                'book_id' => $bookId,
                'book_index_id' => $bookIndexId,
                'title' => $bookIndex['title'],
                'page' => $bookIndex['page'],
            ]);

            if (!empty($subindices)) {
                $this->createBookIndices($bookId, $subindices, $newBookIndex->id);
            }
        }
    }

}
