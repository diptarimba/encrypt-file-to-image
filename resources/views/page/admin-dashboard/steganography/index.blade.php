@extends('layout.app')


@section('page-link', route('admin.encrypt.index'))
@section('page-title', 'Data')
@section('sub-page-title', 'Admin')

@section('content')
    <x-util.card title="Admin" add url="{{route('admin.encrypt.create')}}">
        <table id="datatable" class="table w-full pt-4 text-gray-700 dark:text-zinc-100 datatables-target-exec">
            <thead>
                <tr>
                    <th class="p-4 pr-8 border rtl:border-l-0 border-y-2 border-gray-50 dark:border-zinc-600">Id</th>
                    <th class="p-4 pr-8 border border-y-2 border-gray-50 dark:border-zinc-600 border-l-0">Waktu Enkripsi</th>
                    <th class="p-4 pr-8 border border-y-2 border-gray-50 dark:border-zinc-600 border-l-0">Gambar</th>
                    </th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </x-util.card>
@endsection

@section('custom-footer')
<x-datatables.single url="{{route('admin.encrypt.index')}}">
    <x-datatables.column name="created_at"/>
    <x-datatables.column name="encrypted_image"/>
</x-datatables.single>
@endsection