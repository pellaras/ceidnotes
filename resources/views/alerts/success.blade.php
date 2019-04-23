@if ($message = session()->get('success') ?? session()->get('status') )
  <article class="message is-success">
    <div class="message-body">
      {!! $message !!}
    </div>
  </article>
@endif
