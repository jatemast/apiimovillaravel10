<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Book;
use App\Services\ImgbbService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Mail;
use App\Mail\PurchaseConfirmationEmail;


class BookController extends Controller
{
    protected $imgbbService;

    public function __construct(ImgbbService $imgbbService)
    {
        $this->imgbbService = $imgbbService;
    }

    public function index()
    {
        $books = Book::all();
        return response()->json($books);
    }

    public function show(Book $book)
    {
        return response()->json($book);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'image' => 'nullable|string', // Se espera base64
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $image_url = null;
        if ($request->has('image')) {
            try {
                $image_url = $this->imgbbService->uploadImage($request->image);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error al subir la imagen: ' . $e->getMessage()], 500);
            }
        }

        $book = Book::create([
            'name' => $request->name,
            'quantity' => $request->quantity,
            'price' => $request->price,
            'image_url' => $image_url,
        ]);

        return response()->json($book, 201);
    }

    public function update(Request $request, Book $book)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|required|string|max:255',
            'quantity' => 'sometimes|required|integer|min:0',
            'price' => 'sometimes|required|numeric|min:0',
            'image' => 'nullable|string', // Se espera base64
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $image_url = $book->image_url;
        if ($request->has('image')) {
            try {
                $image_url = $this->imgbbService->uploadImage($request->image);
            } catch (\Exception $e) {
                return response()->json(['message' => 'Error al subir la imagen: ' . $e->getMessage()], 500);
            }
        }

        $book->update([
            'name' => $request->name ?? $book->name,
            'quantity' => $request->quantity ?? $book->quantity,
            'price' => $request->price ?? $book->price,
            'image_url' => $image_url,
        ]);

        return response()->json($book);
    }

    public function destroy(Book $book)
    {
        $book->delete();
        return response()->json(['message' => 'Libro eliminado correctamente']);
    }

    public function purchase(Request $request, Book $book)
    {
        // Verificar si hay suficiente stock
        if ($book->quantity < 1) {
            return response()->json(['message' => 'Lo sentimos, este libro está agotado.'], 400);
        }

        // Simular la compra: reducir la cantidad del libro
        $book->quantity -= 1;
        $book->save();

        // Enviar correo de confirmación de compra (ticket)
        Mail::to($request->user()->email)->send(new PurchaseConfirmationEmail($request->user(), $book));

        return response()->json(['message' => 'Compra realizada con éxito. Se ha enviado un ticket a su correo.'], 200);
    }

    public function search(Request $request)
    {
        $request->validate([
            'query' => 'required|string|min:1',
        ]);

        $query = $request->input('query');

        $books = Book::where('name', 'LIKE', '%' . $query . '%')
                     ->get();

        return response()->json($books);
    }
}
