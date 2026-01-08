<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProductController extends Controller
{
    /**
     * Exibe a listagem de produtos para o front-end Inertia.
     */
    public function index(): Response
    {
        $products = Product::query()
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'description',
                'price',
                'stock',
                'created_at',
            ]);

        return Inertia::render('Products/Index', [
            'products' => $products,
        ]);
    }

    /**
     * Exibe o formulário de criação.
     */
    public function create(): Response
    {
        return Inertia::render('Products/Create');
    }

    /**
     * Persiste um novo produto.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        Product::create($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto cadastrado com sucesso.');
    }

    /**
     * Exibe o formulário de edição.
     */
    public function edit(Product $product): Response
    {
        return Inertia::render('Products/Edit', [
            'product' => $product,
        ]);
    }

    /**
     * Atualiza os dados do produto.
     */
    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
        ]);

        $product->update($validated);

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto atualizado com sucesso.');
    }

    /**
     * Remove um produto existente.
     */
    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();

        return redirect()
            ->route('products.index')
            ->with('success', 'Produto removido.');
    }
}
