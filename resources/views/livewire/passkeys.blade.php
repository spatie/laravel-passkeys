<div>
    <div>
        Let's create a passkey
    </div>
    <div class="mt-2">
        <form id="passkeyForm" wire:submit="validatePasskeyProperties">
            <div>
                <label for="name">name</label>
                <input type="text" wire:model="name">
                @error('name')
                    <span>{{ $message }}</span>
                @enderror
            </div>

            <button type="submit">Create</button>
        </form>
    </div>

    <div>
        <ul>
            @foreach($passkeys as $passkey)
                <li>
                    <div>
                        {{ $passkey->name }}
                    </div>
                    <div>
                        <button wire:click="deletePasskey({{ $passkey->id }})">Delete</button>
                    </div>
                </li>
        </ul>
        @endforeach
    </div>

    @include('passkeys::livewire.partials.createScript')
</div>

