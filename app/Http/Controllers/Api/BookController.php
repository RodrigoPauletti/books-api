<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
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
    public function index(Request $request) {
        $searchByTitle = $request->input('title');
        $searchByBookIndicesTitle = $request->input('index_title');

        try {
            $books = Book
                ::with([
                    'author',
                    'indices'
                ])
                ->when($searchByTitle, function ($q) use ($searchByTitle) {
                    $q->where('title', 'LIKE', '%'.$searchByTitle.'%');
                })
                ->get();

            if ($searchByBookIndicesTitle) {
                $books = $books->filter(function ($book) use ($searchByBookIndicesTitle) {
                    return $this->filterBookIndicesByTitle($book->indices, $searchByBookIndicesTitle)->isNotEmpty();
                });
            }

            return response()->json($books, 200);
        } catch (Exception $exception) {
            return response()->json([
                'message' => $exception->getMessage(),
            ], 500);
        }
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
            ], 201);
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

    private function filterBookIndicesByTitle($bookIndices, $title) {
        return $bookIndices->filter(function ($bookIndex) use ($title) {
            if (stripos($bookIndex->title, $title) !== false) {
                return true;
            }

            // Recursively check subindices
            return $this->filterBookIndicesByTitle($bookIndex->subindices, $title)->isNotEmpty();
        });
    }

}
