<x-layout :pageTitle="$pageTitle" :users="$users" :currentUserId="$currentUserId" :userDocCounts="$userDocCounts" :currentCategory="$currentCategory" :documents="$documents">
	@php
		$documentCount = count($documents);
		error_log('Document count in view: ' . $documentCount . ' for user: ' . $currentUserId . ' and category: ' . ($currentCategory ?? 'All'));
	@endphp
	<div class="content">
		@if (session('message'))
			<div class="upload-form__alert upload-form__alert--success">
				{{ session('message') }}
			</div>
		@endif
		<div class="content-header">
			<h1>Documents</h1>
			@if (isset($currentCategory) && !empty($currentCategory))
				<div class="category-header">
					<h2>
						Category: {{ htmlspecialchars($currentCategory) }}
						<div class="document-counter">
							<div class="document-counter-inner">
								<span class="counter-number">{{ $documentCount }}</span>
								<span class="counter-text">DOCUMENTS</span>
							</div>
						</div>
					</h2>
					<a href="/?route=list&user_id={{ $currentUserId }}" class="show-all-btn">
						📄 Show All Documents
					</a>
				</div>
			@else
				<div class="category-header">
					<h2>
						All Documents
						<div class="document-counter">
							<div class="document-counter-inner">
								<span class="counter-number">{{ $documentCount }}</span>
								<span class="counter-text">DOCUMENTS</span>
							</div>
						</div>
					</h2>
				</div>
			@endif
			<div class="search-container">
				<form action="/" method="GET" class="search-form">
					<input type="hidden" name="route" value="list">
					<input type="hidden" name="user_id" value="{{ $currentUserId }}">
					@if (isset($currentCategory) && !empty($currentCategory))
						<input type="hidden" name="category" value="{{ htmlspecialchars($currentCategory) }}">
					@endif
					<input type="text" name="search" placeholder="Search documents..." value="{{ isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' }}" class="form-control">
					<button type="submit" class="btn btn--primary">Search</button>
					<a href="{{ isset($currentCategoryId) && $currentCategoryId ? route('documents.create', ['category_id' => $currentCategoryId]) : route('documents.create') }}" class="upload-button">Upload Document</a>
				</form>
			</div>
		</div>


		@if (empty($documents))
			@if (isset($currentCategory) && !empty($currentCategory))
				<div class="empty-state">
					<div class="document-icon">📄</div>
					<p>No documents found in category "{{ htmlspecialchars($currentCategory) }}".</p>
					<p class="sub-message">Upload a document to this category to see it here.</p>
					<a href="{{ isset($currentCategoryId) && $currentCategoryId ? route('documents.create', ['category_id' => $currentCategoryId]) : route('documents.create') }}" class="btn-primary">Upload to this category</a>
				</div>
			@else
				<div class="empty-state">
					<div class="document-icon">📄</div>
					<p>No documents found.</p>
					<p class="sub-message">Start by uploading a document using the form below.</p>
					<a href="{{ isset($currentCategoryId) && $currentCategoryId ? route('documents.create', ['category_id' => $currentCategoryId]) : route('documents.create') }}" class="btn-primary">Upload a document</a>
				</div>
			@endif
		@else
			<div class="document-list" id="documentList">
				@foreach ($documents as $doc)
					<div class="document-item" data-id="{{ $doc['id'] }}" data-user-id="{{ $currentUserId }}">
						<div class="document-item-content" onclick="window.location='{{ route('documents.show', $doc) }}'">
							<div class="document-icon">
								@php
									$fileType = $doc['file_type'] ?? '';
									$fileSymbol = '📄'; // Default document symbol

									if (strpos($fileType, 'pdf') !== false) {
									    $fileSymbol = '📕'; // PDF symbol
									} elseif (strpos($fileType, 'image') !== false) {
									    $fileSymbol = '🖼️'; // Image symbol
									} elseif (strpos($fileType, 'word') !== false || strpos($fileType, 'document') !== false) {
									    $fileSymbol = '📝'; // Word document symbol
									} elseif (strpos($fileType, 'excel') !== false || strpos($fileType, 'spreadsheet') !== false) {
									    $fileSymbol = '📊'; // Excel/spreadsheet symbol
									} elseif (strpos($fileType, 'text') !== false) {
									    $fileSymbol = '📃'; // Text file symbol
									}
								@endphp
								<span class="document-icon-symbol">{{ $fileSymbol }}</span>
							</div>
							<div class="document-details">
								<h3 class="document-title">{{ htmlspecialchars($doc['title']) }}</h3>
								<p class="document-description">{{ htmlspecialchars($doc['description'] ?? '') }}
								</p>
								<div class="document-meta">
									<div class="document-meta__dates">
										<span class="document-uploaded-date">
											📅 {{ date('M d, Y', strtotime($doc['upload_date'])) }}
										</span>
										<span class="document-created-date">
											✍️ {{ date('M d, Y', strtotime($doc['created_date'])) }}
										</span>
									</div>
									<span class="document-category">
										📁
										{{ $doc->category->name }}
									</span>
									<span class="document-size">
										📦 {{ number_format($doc['file_size'] / 1024, 2) }} KB
									</span>
								</div>
							</div>
						</div>
						<div class="document-item__actions">
              <a href="{{ route('documents.edit', $doc) }}" class="document-item__btn document-item__btn--edit">
								✏️
							</a>
							<form method="POST" action="{{ route('documents.destroy', $doc) }}" onsubmit="return confirm('Are you sure you want to delete this document?');" onclick="event.stopPropagation();">
								@csrf
								@method('DELETE')
								<input type="hidden" name="user_id" value="{{ $currentUserId }}">
								@if (isset($currentCategory))
									<input type="hidden" name="category" value="{{ htmlspecialchars($currentCategory) }}">
								@endif
								<button type="submit" class="document-item__btn document-item__btn--delete" title="Delete document" onclick="event.stopPropagation();">🗑️</button>
							</form>
						</div>
					</div>
				@endforeach

				<div class="main-pagination">
					{{ $documents->links() }}
				</div>
			</div>

			<div class="document-upload">
				<a href="{{ isset($currentCategoryId) && $currentCategoryId ? route('documents.create', ['category_id' => $currentCategoryId]) : route('documents.create') }}" class="btn btn--primary">
					➕ Upload New Document
				</a>
			</div>
		@endif
	</div>
</x-layout>

{{-- @extends('layouts.main')

@section('content')
<div class="content-wrapper">
  <h1>Welcome to MyDocs</h1>

  <p>This is the home page of your document management system.</p>

  <div class="document-section">
    <h2>Recent Documents</h2>
  </div>
</div>
@endsection --}}
