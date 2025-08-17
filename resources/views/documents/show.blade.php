<x-layout :pageTitle="$pageTitle" :users="$users" :currentUserId="Auth::id()" :userDocCounts="$userDocCounts" :currentCategory="$document->category_id" :documents="$documents">
 <div class="document-view">
  @if (isset($document) && $document)
   <div class="document-view__details">
    <h3 class="document-view__subtitle">{{ $document->title }}</h3>
    <table class="document-view__table">
     @if (!empty($document->created_date))
      <tr class="document-view__row">
       <td class="document-view__label">Created at:</td>
       <td class="document-view__value">
        <span class="document-view__date">{{ \Carbon\Carbon::parse($document->created_date)->format('M d, Y') }}</span>
       </td>
      </tr>
     @endif
     <tr class="document-view__row">
      <td class="document-view__label">Uploaded at:</td>
      <td class="document-view__value">
       @php
        $uploadDate = $document->created_at ? \Carbon\Carbon::parse($document->created_at) : null;
       @endphp
       @if ($uploadDate)
        {{ $uploadDate->format('M d, Y') }} <span class="document-view__time">{{ $uploadDate->format('H:i') }}</span>
       @endif
      </td>
     </tr>
     @if (!empty($document->description))
      <tr class="document-view__row">
       <td class="document-view__label">Description:</td>
       <td class="document-view__value">
        <div class="document-view__description">
         {!! nl2br(e($document->description)) !!}
        </div>
       </td>
      </tr>
     @endif
     <tr class="document-view__row">
      <td class="document-view__label">Category:</td>
      <td class="document-view__value">
       <span class="document-view__category">{{ $document->category->name ?? 'Uncategorized' }}</span>
      </td>
     </tr>
     <tr class="document-view__row">
      <td class="document-view__label">Owner:</td>
      <td class="document-view__value">
       @php
        try {
            $owner = $document->user;
            echo $owner ? $owner->first_name . ' ' . $owner->last_name : 'Unknown';
        } catch (Exception $e) {
            echo 'Unknown';
        }
       @endphp
      </td>
     </tr>
    </table>
   </div>

   <div class="document-view__content">
    @auth
     @if (isset($document->filename) && pathinfo($document->filename, PATHINFO_EXTENSION) === 'txt')
      <h3 class="document-view__subtitle">Document Content:</h3>
      @php
       $absolutePath =
           env('DOCKER_ENV') === 'true' ? $document->file_path : base_path($localPath ?? $document->file_path);
      @endphp
      @if (file_exists($absolutePath))
       <pre class="document-view__text-content">{{ file_get_contents($absolutePath) }}</pre>
      @else
       <div class="document-view__error">Unable to display file content. The file may have been moved or deleted.</div>
      @endif
     @else
      <div class="document-view__file-info">
       <p class="document-view__file-path">
        <strong>File Path:</strong>
        <span class="path-container">
         <code id="filePath" class="copyable-path" data-path="{{ $filePublicUrl }}"
          data-dir-path="{{ $windowsDirectoryPath ?? dirname($document->file_path) }}"
          title="Click buttons to copy URL or folder path">
          {{ $displayPath ?? $document->file_path }}
         </code>
        </span>
       <div class="copy-buttons">
        <button id="copyFileBtn" class="copy-btn" title="Copy file URL to clipboard">
         📄 Copy File URL
        </button>
        <button id="copyDirBtn" class="copy-btn" title="Copy directory path to clipboard">
         📁 Copy Folder Path
        </button>
       </div>
       </span>
       <span id="copyMessage" style="display: none; color: green; margin-left: 10px; font-size: 0.9em;">Copied to
        clipboard!</span>
      </div>
      <script>
       document.addEventListener('DOMContentLoaded', function() {
        const copyFileBtn = document.getElementById('copyFileBtn');
        const copyDirBtn = document.getElementById('copyDirBtn');
        const pathElement = document.getElementById('filePath');
        const message = document.getElementById('copyMessage');

        console.log('File path data attribute:', pathElement.getAttribute('data-path'));
        console.log('Folder path data attribute:', pathElement.getAttribute('data-dir-path'));

        // Function to copy text to clipboard
        function copyToClipboard(text, successMessage) {
         // Log to console for debugging
         console.log('Attempting to copy:', text);

         // Use the modern clipboard API
         if (navigator.clipboard) {
          navigator.clipboard.writeText(text)
           .then(() => {
            console.log('Copy successful');
            showCopyMessage(successMessage);
           })
           .catch(err => {
            console.error('Failed to copy text: ', err);
            alert('Could not copy to clipboard. Your browser might be blocking this feature.');
           });
         } else {
          // Fallback for older browsers
          const textarea = document.createElement('textarea');
          textarea.value = text;
          textarea.style.position = 'fixed';
          textarea.style.opacity = 0;
          document.body.appendChild(textarea);
          textarea.focus();
          textarea.select();

          try {
           const successful = document.execCommand('copy');
           if (successful) {
            console.log('Fallback copy successful');
            showCopyMessage(successMessage);
           } else {
            console.error('Fallback copy failed');
            alert('Could not copy to clipboard');
           }
          } catch (err) {
           console.error('Fallback copy error: ', err);
           alert('Could not copy to clipboard');
          }

          document.body.removeChild(textarea);
         }
        }

        // Show copy message
        function showCopyMessage(successMsg) {
         message.textContent = successMsg || 'Copied to clipboard!';
         message.style.display = 'inline-block';
         setTimeout(() => {
          message.style.display = 'none';
         }, 2000);
        }

        // Event listeners for copy buttons
        copyFileBtn.addEventListener('click', function() {
         const filePath = pathElement.getAttribute('data-path');
         copyToClipboard(filePath, 'File path copied to clipboard!');
        });

        copyDirBtn.addEventListener('click', function() {
         const dirPath = pathElement.getAttribute('data-dir-path');
         copyToClipboard(dirPath, 'Folder path copied to clipboard!');
        });
       });
      </script>
     @endif
    @endauth
    @guest
     <div class="notice notice--subtle notice--muted">
      <div class="notice__icon">ℹ️</div>
      <div class="notice__content">
       <p class="notice__text">
        File path: <span class="path-chip">{{ $document->file_path }}</span>
       </p>
      </div>
     </div>
    @endguest
   </div>

   <section class="document-actions">
    @auth
     <a href="{{ $filePublicUrl }}" target="_blank" class="document-actions__btn document-actions__btn--view">
      <span class="document-actions__icon">👁️</span>
      <span class="document-actions__text">View</span>
     </a>
    @endauth
    @can('edit-document', $document)
     <a href="{{ route('documents.edit', $document) }}" class="document-actions__btn document-actions__btn--edit">
      <span class="document-actions__icon">✏️</span>
      <span class="document-actions__text">Edit</span>
     </a>
    @endcan
    @auth
     <a href="{{ route('documents.download', $document) }}"
      class="document-actions__btn document-actions__btn--download">
      <span class="document-actions__icon">📥</span>
      <span class="document-actions__text">Download</span>
     </a>
    @endauth
    @can('delete-document', $document)
     <form method="POST" action="{{ route('documents.destroy', $document) }}" class="document-actions__form"
      onsubmit="return confirm('Are you sure you want to delete this document?');" onclick="event.stopPropagation();">
      @csrf
      @method('DELETE')

      @if (isset($document->category))
       <input type="hidden" name="category" value="{{ $document->category->name }}">
      @endif
      <button type="submit" class="document-actions__btn document-actions__btn--delete" title="Delete document"
       onclick="event.stopPropagation();">
       <span class="document-actions__icon">🗑️</span>
       <span class="document-actions__text">Delete</span>
      </button>
     </form>
    @endcan
    @guest
     <div class="notice notice--danger notice--muted">
      <div class="notice__icon">🔒</div>
      <div class="notice__content">
       <p class="notice__text">
        Please log in to view or download this file.
        <a href="{{ route('auth.login', ['return' => url()->full()]) }}" class="btn btn-primary btn--sm" aria-label="Log in to your account">Log in</a>
       </p>
      </div>
     </div>
    @endguest
   </section>

   <div class="document-view__back">
    <a href="{{ route('documents.index') }}" class="document-view__back-link-simple">Back to Document List</a>
   </div>
  @else
   <p class="document-view__not-found">Document not found.</p>
  @endif
 </div>
</x-layout>
