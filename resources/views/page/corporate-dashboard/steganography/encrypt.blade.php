@extends('layout.app')

@section('page-link', $data['home'])
@section('page-title', 'Encrypt')
@section('sub-page-title', 'Data')

@section('content')
    <x-util.card title="{{ $data['title'] }}">
        <x-form.base url="{{ $data['url'] }}" method="POST">
            <x-form.input accept=".jpg,.png,.jpeg,.pdf,.docx,.txt,.csv,.xlsx" name="file" type="file"
                label="File to Encrypt" placeholder="" value="" />
            <div class="mb-4">
                <label for="image-stock" class="block font-medium text-gray-700 dark:text-gray-100 mb-2">Selected
                    image</label>
                <div id="image-stock"class="flex items-center justify-center bg-gray-100 rounded-lg">
                    <div class="w-full">
                        <div id="carousel" class="flex space-x-4 overflow-x-auto p-4 bg-white rounded-lg shadow-lg">
                            @foreach ($images as $eachimage)
                                <img src="{{ $eachimage }}" alt="Image 1" class="w-32 h-32 object-cover cursor-pointer"
                                    onclick="selectImage(this, '{{ $eachimage }}')">
                            @endforeach
                            <!-- Tambahkan lebih banyak gambar sesuai kebutuhan -->
                        </div>
                        <input type="hidden" name="imagedefault" id="imagedefault" value="">
                    </div>
                </div>
            </div>
            <x-form.input accept=".png" texting="Image" name="image" type="file" label="Image to Camuflase (.png)"
                placeholder="" value="" />
            <div class="mb-4 hidden" id="thumbnailContainer">
                <img id="thumbnail" src="" alt="Thumbnail" class="max-w-xs mb-2">
                <button type="button" id="removeButton"
                    class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">Remove Image</button>
            </div>
            <x-form.input name="password" type="password" label="Password Encrypt" placeholder="" value="" />
            <x-form.input name="watermark_text" type="text" label="Watermark" placeholder="" value="" />
            <x-button.submit />
            <x-button.cancel url="{{ $data['home'] }}" />
        </x-form.base>
    </x-util.card>
@endsection

@section('custom-footer')
    <script>
        function selectImage(img, value) {

            imageFileInput.value = '';
            // Hide the thumbnail container
            thumbnailContainer.classList.add('hidden');

            const label = document.querySelectorAll('.filename');
            label[1].classList.add('hidden');
            // Hapus outline dari gambar yang lain
            document.querySelectorAll('#carousel img').forEach(function(image) {
                image.classList.remove('selected');
                image.classList.remove('border-2');
                image.classList.remove('border-violet-500');
            });
            // Tambahkan outline ke gambar yang dipilih
            img.classList.add('selected');
            img.classList.add('border-2');
            img.classList.add('border-violet-500');
            // Set nilai input
            document.getElementById('imagedefault').value = value;
        }
    </script>
    <script>
        // Select the file input element
        const imageFileInput = document.getElementById('input-image');
        // Select the thumbnail container element
        const thumbnailContainer = document.getElementById('thumbnailContainer');
        // Select the thumbnail element
        const thumbnail = document.getElementById('thumbnail');
        // Select the remove button element
        const removeButton = document.getElementById('removeButton');

        // When a file is selected
        imageFileInput.addEventListener('change', function(event) {
            // Get the selected file
            const file = event.target.files[0];

            // Check if a file is selected
            if (file) {
                // Show the thumbnail container
                thumbnailContainer.classList.remove('hidden');

                // Create a FileReader object
                const reader = new FileReader();

                // When the FileReader has finished reading the file
                reader.onload = function(e) {
                    // Set the thumbnail source as the result of the FileReader
                    thumbnail.setAttribute('src', e.target.result);
                };

                document.querySelectorAll('#carousel img').forEach(function(image) {
                    image.classList.remove('selected');
                    image.classList.remove('border-2');
                    image.classList.remove('border-violet-500');
                });

                document.getElementById('imagedefault').value = '';

                // Read the file as a data URL
                reader.readAsDataURL(file);
            } else {
                // Hide the thumbnail container if no file is selected
                thumbnailContainer.classList.add('hidden');
            }
        });

        // When the remove button is clicked
        removeButton.addEventListener('click', function() {
            // Reset the file input value
            imageFileInput.value = '';
            // Hide the thumbnail container
            thumbnailContainer.classList.add('hidden');
        });
    </script>
@endsection
