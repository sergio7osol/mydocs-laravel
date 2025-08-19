<div style="background:#f6f7fb;padding:24px 0;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#1f2937;">
  <div style="max-width:600px;margin:0 auto;background:#ffffff;border:1px solid #e5e7eb;border-radius:8px;overflow:hidden;">
    <div style="padding:20px 24px;border-bottom:1px solid #eee;background:#0f172a;color:#fff;">
      <h1 style="margin:0;font-size:18px;letter-spacing:.2px;">New document uploaded</h1>
    </div>

    <div style="padding:24px;">
      <p style="margin:0 0 12px;">Hello {{ $document->category?->user?->name ?? 'there' }},</p>
      <p style="margin:0 0 16px;">
        A new document has been uploaded to
        <strong>{{ $document->category?->name ?? 'Uncategorized' }}</strong>.
      </p>

      <table role="presentation" style="width:100%;border-collapse:collapse;font-size:14px;margin:12px 0 8px;">
        <tbody>
          <tr>
            <td style="padding:6px 0;width:140px;color:#6b7280;">Title</td>
            <td style="padding:6px 0;"><strong>{{ $document->title }}</strong></td>
          </tr>

          @if(!empty($document->description))
          <tr>
            <td style="padding:6px 0;color:#6b7280;">Description</td>
            <td style="padding:6px 0;">{{ $document->description }}</td>
          </tr>
          @endif

          <tr>
            <td style="padding:6px 0;color:#6b7280;">Uploaded by</td>
            <td style="padding:6px 0;">
              {{ $document->user?->name ?? $document->user?->email ?? 'Unknown' }}
            </td>
          </tr>

          @if(!empty($document->created_date))
          <tr>
            <td style="padding:6px 0;color:#6b7280;">Document date</td>
            <td style="padding:6px 0;">{{ $document->created_date }}</td>
          </tr>
          @endif

          <tr>
            <td style="padding:6px 0;color:#6b7280;">File</td>
            <td style="padding:6px 0;">
              {{ $document->filename }}
              @if(!empty($document->file_size))
                ({{ number_format($document->file_size / 1024, 0) }} KB)
              @endif
            </td>
          </tr>
        </tbody>
      </table>

      <p style="margin:20px 0;">
        <a href="{{ route('documents.show', $document->id) }}"
           style="display:inline-block;background:#2563eb;color:#fff;text-decoration:none;padding:10px 16px;border-radius:6px;">
          View document
        </a>
      </p>

      <p style="font-size:12px;color:#6b7280;margin:0;">
        If the button doesn’t work, copy and paste this URL into your browser:<br>
        <span style="word-break:break-all;">{{ route('documents.show', $document->id) }}</span>
      </p>
    </div>

    <div style="padding:14px 24px;border-top:1px solid #eee;background:#f9fafb;font-size:12px;color:#6b7280;">
      MyDocs • {{ config('app.name') }} • {{ config('app.url') }}
    </div>
  </div>
</div>
