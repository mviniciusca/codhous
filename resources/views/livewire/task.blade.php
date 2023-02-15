  <div class="rounded-2xl bg-white p-8">
      <div class="flex items-center justify-between gap-3 text-sm font-bold">

          <div class="flex items-center gap-3 text-base">

              <ion-icon class="text-lg" name="bookmarks-outline"></ion-icon>
              Latest Tasks <span
                  class="flex items-center rounded-lg bg-gray-800 p-1 text-xs font-bold text-gray-100">{{ $total }}</span>
          </div>
          <div class="flex gap-3">
              <button>
                  <ion-icon class="text-lg" name="chevron-back-outline"></ion-icon>
              </button>
              <button>
                  <ion-icon class="text-lg" name="chevron-forward-outline"></ion-icon>
              </button>
          </div>
      </div>
      <div class="mt-8">
          @foreach ($items as $item)
              <div class="mb-5 flex items-center justify-between gap-14">
                  <label for="{{ $item->id }}">
                      <input name="status" id="{{ $item->id }}" type="checkbox">
                      {{ $item->title }}
                  </label>
                  <livewire:task.destroy :key="$item->id" />
              </div>
          @endforeach
      </div>
  </div>
