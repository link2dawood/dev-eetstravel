@php
use App\Tour;
$tours = Tour::query()->orderBy('name')->get(['id', 'name']);
@endphp
<div id="modalCreate" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modalCreateLabel">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCreateLabel">Move email</h5>
                <button type="button" class="btn-close" aria-label="Close" @click="CloseMoveToModal"></button>
            </div>
            <div class="modal-body">
                <label class="form-label" for="move-email-target">Move to</label>
                {{-- plain select: Select2 would break the v-model binding --}}
                <select id="move-email-target" name="folder" class="form-select" v-model="folderSend" v-if="folders && folders.INBOX">
                    <optgroup label="Mail folders">
                        <option value="INBOX">Inbox</option>
                        <option v-for="(folder, index) in folders.INBOX" v-if="index !== 'Drafts'" :value="index">@{{ index }}</option>
                    </optgroup>
                    <optgroup label="Tours (file the email under a tour)">
                        @foreach($tours as $tour)
                            <option value="{{ $tour->id }}">{{ $tour->name }}</option>
                        @endforeach
                    </optgroup>
                </select>
                <div class="text-muted small" v-else>Loading folders...</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link link-secondary me-auto" @click="CloseMoveToModal">Cancel</button>
                <button type="button" class="btn btn-primary" @click="moveToFolder"><i class="fa fa-folder-open me-1"></i>Move</button>
            </div>
        </div>
    </div>
</div>
