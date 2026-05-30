{{-- Single-email view fragment, rendered inside #email_box Vue root.
     All Vue directives (@click, v-html, v-for, v-if) preserved as-is. --}}
<div class="mailbox-controls text-right border-b border-slate-200 px-5 py-3 flex items-center justify-end gap-2">
    <button type="button" @click="backToList"
            class="inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
        <x-ui.icon name="arrow-left" size="sm" /> Back
    </button>
    <button type="button" @click="replyEmail(email)"
            class="inline-flex items-center gap-1 rounded border border-slate-300 bg-white px-3 h-9 text-sm text-slate-700 hover:bg-slate-50">
        <x-ui.icon name="corner-up-left" size="sm" /> Reply
    </button>
    <a data-toggle="modal" @click="deleteMail(email, true)"
       class="btn-sm inline-flex items-center gap-1 rounded border border-danger-300 bg-white px-3 h-9 text-sm text-danger-600 hover:bg-danger-50 cursor-pointer">
        <x-ui.icon name="trash" size="sm" /> Delete
    </a>
</div>

<div class="px-5 py-4">
    <div class="mailbox-read-info border-b border-slate-200 pb-3">
        <h3 class="text-base font-semibold text-slate-900 m-0">@{{ email.header.subject }}</h3>
        <h5 class="mt-2 text-xs text-slate-500 flex items-center justify-between gap-2 m-0">
            <span>From: <span class="font-medium text-slate-700">@{{ email.header.from }}</span></span>
            <span class="mailbox-read-time">@{{ moment(email.header.date).format('YYYY-MM-DD H:m:s') }}</span>
        </h5>
    </div>

    <div class="mailbox-read-message prose prose-sm max-w-none mt-4 text-slate-800">
        <span v-html="(email.message.html) ? email.message.html.body : email.message.text"></span>
    </div>
</div>

<div class="attach-block px-5 pb-4" v-if="email.attachments.length > 0">
    <div class="rounded border border-slate-200 bg-white">
        <div class="border-b border-slate-200 px-4 py-2 flex items-center gap-2">
            <x-ui.icon name="paperclip" size="sm" class="text-slate-400" />
            <h3 class="text-sm font-medium text-slate-700 m-0">Attachments</h3>
        </div>
        <ul class="mailbox-attachments list-none m-0 p-0 divide-y divide-slate-100">
            <li v-for="attach in email.attachments" class="flex items-center gap-2 px-4 py-2">
                <span class="mailbox-attachment-icon flex h-7 w-7 items-center justify-center rounded bg-slate-100 text-slate-500">
                    <x-ui.icon name="file" size="sm" />
                </span>
                <div class="mailbox-attachment-info flex-1 min-w-0">
                    <a @click="downloadAttachment(attach)" class="mailbox-attachment-name inline-flex items-center gap-1 text-sm text-primary-600 hover:text-primary-700 cursor-pointer">
                        <x-ui.icon name="paperclip" size="xs" /> @{{ attach.name }}
                    </a>
                </div>
            </li>
        </ul>
    </div>
</div>
