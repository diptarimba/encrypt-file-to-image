@extends('layout.app')

@section('page-link', $data['home'])
@section('page-title', 'Decrypt')
@section('sub-page-title', 'Upload')

@section('content')
    <x-util.card title="{{ $data['title'] }}">
        <x-form.base url="{{ $data['url'] }}" method="POST">
            <x-form.input accept=".png" texting="Image" name="encrypted_image" type="file" label="Encrypted Image (.PNG)"
                placeholder="" value="" />
            <div class="mb-4 hidden" id="thumbnailContainer">
                <img id="thumbnail" src="" alt="Thumbnail" class="max-w-xs mb-2">
                <button type="button" id="removeButton"
                    class="bg-red-500 hover:bg-red-700 text-white px-4 py-2 rounded">Remove Image</button>
            </div>
            <x-button.submit />
            <x-button.cancel url="{{ $data['home'] }}" />
        </x-form.base>
    </x-util.card>
@endsection

@section('custom-footer')
    <script>
        // Select the file input element
        const imageFileInput = document.getElementById('input-encrypted_image');
        // Select the thumbnail container element
        const thumbnailContainer = document.getElementById('thumbnailContainer');
        // Select the thumbnail element
        const thumbnail = document.getElementById('thumbnail');
        // Select the remove button element
        const removeButton = document.getElementById('removeButton');

        const labelFileName = document.querySelectorAll('.filename');

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

                // Read the file as a data URL
                reader.readAsDataURL(file);
            } else {
                // Hide the thumbnail container if no file is selected
                thumbnailContainer.classList.add('hidden');
            }
        });

        // When the remove button is clicked
        removeButton.addEventListener('click', function() {
            labelFileName[0].classList.add('hidden');
            // Reset the file input value
            imageFileInput.value = '';
            // Hide the thumbnail container
            thumbnailContainer.classList.add('hidden');
        });
    </script>
@endsection
