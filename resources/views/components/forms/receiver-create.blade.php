<div class="card border-0 shadow-sm">
  <div class="card-header border-0 bg-white py-3 d-flex justify-content-between align-items-center">
    <h5 class="mb-0 fw-semibold"><b>{{ __('Create New Receiver') }}</b></h5>
  </div>
  <hr class="mt-0"/>

  <div class="card-body pt-0">
    {{-- Enhanced MTCN Input --}}
    <div class="mb-4">
      <div class="mtcn-container">
        <div class="mtcn-header">
          <div class="mtcn-title">
            <i class="fas fa-barcode"></i>
            {{ __('MTCN Tracking Number') }}
          </div>
          <div class="mtcn-subtitle">
            {{ __('Enter your 10-digit Money Transfer Control Number') }}
          </div>
        </div>

        <div class="mtcn-input-group">
          <div class="mtcn-field">
            <input type="text" 
                   class="mtcn-input {{ $this->getInputClass('mtcn1') }}" 
                   id="mtcn1" 
                   maxlength="3" 
                   placeholder="123"
                   inputmode="numeric" 
                   pattern="[0-9]*"
                   wire:model.lazy="mtcn1">
          </div>
          <div class="mtcn-separator">—</div>
          <div class="mtcn-field">
            <input type="text" 
                   class="mtcn-input {{ $this->getInputClass('mtcn2') }}" 
                   id="mtcn2" 
                   maxlength="3" 
                   placeholder="456"
                   inputmode="numeric" 
                   pattern="[0-9]*"
                   wire:model.lazy="mtcn2">
          </div>
          <div class="mtcn-separator">—</div>
          <div class="mtcn-field">
            <input type="text" 
                   class="mtcn-input wide {{ $this->getInputClass('mtcn3') }}" 
                   id="mtcn3" 
                   maxlength="4" 
                   placeholder="7890"
                   inputmode="numeric" 
                   pattern="[0-9]*"
                   wire:model.lazy="mtcn3">
          </div>
        </div>

        <div class="progress-bar">
          <div class="progress-fill" id="progressFill" style="width: 0%"></div>
        </div>

        <div class="mtcn-status" id="mtcnStatus">
          <div class="status-incomplete">
            <span id="statusText">{{ __('Enter your MTCN to continue') }}</span>
          </div>
        </div>

        <div class="mtcn-actions">
          <button type="button" class="mtcn-btn mtcn-btn-clear" id="clearBtn">
            <i class="fas fa-eraser"></i>
            {{ __('Clear') }}
          </button>
          <button type="button" class="mtcn-btn mtcn-btn-paste" id="pasteBtn">
            <i class="fas fa-clipboard"></i>
            {{ __('Paste') }}
          </button>
        </div>

        @if($errors->has('mtcn1') || $errors->has('mtcn2') || $errors->has('mtcn3'))
          <div class="text-center mt-3">
            <div class="text-danger">
              <i class="fas fa-exclamation-triangle"></i>
              {{ __('Please complete the MTCN number') }}
            </div>
          </div>
        @endif
      </div>
    </div>

    <form wire:submit.prevent="submit">
      <div class="row g-4">
        {{-- Rest of your form fields remain the same --}}
        {{-- First name --}}
        <div class="col-md-6">
          <label for="rx_first_name" class="form-label">{{ __('First Name') }} <span class="text-danger">*</span></label>
          <input id="rx_first_name" type="text" placeholder="{{ __('FIRST NAME') }}"
                 style="text-transform:uppercase"
                 wire:model.debounce.500ms="first_name"
                 class="{{ $this->getInputClass('first_name') }}" required>
          @error('first_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Last name --}}
        <div class="col-md-6">
          <label for="rx_last_name" class="form-label">{{ __('Last Name') }} <span class="text-danger">*</span></label>
          <input id="rx_last_name" type="text" placeholder="{{ __('LAST NAME') }}"
                 style="text-transform:uppercase"
                 wire:model.debounce.500ms="last_name"
                 class="{{ $this->getInputClass('last_name') }}" required>
          @error('last_name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Phone --}}
        <div class="col-md-6">
          <label for="rx_phone" class="form-label">{{ __('Phone Number') }} <span class="text-danger">*</span></label>
          <input id="rx_phone" placeholder="+9647xxxxxxxx"
                 wire:model.debounce.500ms="phone"
                 type="tel" inputmode="tel" autocomplete="tel"
                 class="{{ $this->getInputClass('phone') }}" required>
          @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Address --}}
        <div class="col-md-6">
          <label for="rx_address" class="form-label">{{ __('Receiver Address') }}</label>
          <input id="rx_address" type="text" placeholder="{{ __('Street, City') }}"
                 wire:model.debounce.500ms="address"
                 class="{{ $this->getInputClass('address') }}">
          @error('address') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Amount (IQD) --}}
        <div class="col-md-6">
          <label for="rx_amount_iqd" class="form-label">{{ __('Amount (IQD)') }} <span class="text-danger">*</span></label>
          <input id="rx_amount_iqd" type="number" step="1" min="1"
                 wire:model.debounce.500ms="amount_iqd"
                 class="{{ $this->getInputClass('amount_iqd') }}" required>
          @error('amount_iqd') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
        </div>

        {{-- Identification (disabled for now) --}}
        <div class="col-md-6">
          <label class="form-label">{{ __('Identification') }}</label>
          <input type="text" class="form-control" placeholder="{{ __('(Optional)') }}" disabled>
          <small class="text-muted">{{ __('We will add ID details later.') }}</small>
        </div>
      </div>

      <div class="d-flex justify-content-end gap-2 mt-4">
        <button type="submit" class="btn btn-primary" wire:loading.attr="disabled">
          <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          {{ __('Save Receiver') }}
        </button>
      </div>
    </form>
  </div>
</div>

@push('css')
<style>
.mtcn-container {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 2rem;
    position: relative;
    overflow: hidden;
}

.mtcn-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 20%, rgba(255,255,255,0.1) 0%, transparent 50%),
        radial-gradient(circle at 80% 80%, rgba(255,255,255,0.05) 0%, transparent 50%);
    pointer-events: none;
}

.mtcn-header {
    position: relative;
    z-index: 2;
}

.mtcn-title {
    color: white;
    font-weight: 700;
    font-size: 1.1rem;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mtcn-subtitle {
    color: rgba(255, 255, 255, 0.8);
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
}

.mtcn-input-group {
    display: flex;
    justify-content: center;
    align-items: center;
    gap: 1rem;
    flex-wrap: wrap;
    position: relative;
    z-index: 2;
}

.mtcn-field {
    position: relative;
}

.mtcn-input {
  padding: 0;
    width: 80px;
    height: 60px;
    font-size: 1.5rem;
    font-weight: 700;
    text-align: center;
    letter-spacing: 0.2em;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-radius: 12px;
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    color: white;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    outline: none;
}

.mtcn-input.wide {
    width: 100px;
}

.mtcn-input::placeholder {
    color: rgba(255, 255, 255, 0.5);
    font-weight: 400;
}

.mtcn-input:focus {
    border-color: #4ade80 !important;
    background: rgba(255, 255, 255, 0.15) !important;
    color: white !important;
    box-shadow: 
        0 0 0 3px rgba(74, 222, 128, 0.2),
        0 8px 25px rgba(0, 0, 0, 0.15);
    transform: translateY(-2px);
}

.mtcn-input.is-valid {
  padding: 0;
    border-color: #4ade80 !important;
    background: rgba(74, 222, 128, 0.1) !important;
    color: white !important;
}

.mtcn-input.is-invalid {
  padding: 0;
    border-color: #ef4444 !important;
    background: rgba(239, 68, 68, 0.1) !important;
    color: white !important;
    animation: shake 0.5s ease-in-out;
}

.mtcn-separator {
    color: rgba(255, 255, 255, 0.6);
    font-size: 1.5rem;
    font-weight: 300;
    user-select: none;
}

.mtcn-actions {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-top: 1.5rem;
    position: relative;
    z-index: 2;
}

.mtcn-btn {
    padding: 0.75rem 1.5rem;
    border-radius: 10px;
    border: none;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.mtcn-btn-clear {
    background: rgba(255, 255, 255, 0.1);
    color: rgba(255, 255, 255, 0.8);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.mtcn-btn-clear:hover {
    background: rgba(255, 255, 255, 0.15);
    color: white;
}

.mtcn-btn-paste {
    background: rgba(74, 222, 128, 0.2);
    color: white;
    border: 1px solid rgba(74, 222, 128, 0.3);
}

.mtcn-btn-paste:hover {
    background: rgba(74, 222, 128, 0.3);
    transform: translateY(-1px);
}

.mtcn-status {
    text-align: center;
    margin-top: 1rem;
    font-size: 0.875rem;
    position: relative;
    z-index: 2;
}

.status-complete {
    color: #4ade80;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

.status-incomplete {
    color: rgba(255, 255, 255, 0.6);
}

.progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 2px;
    margin-top: 1rem;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #4ade80, #22c55e);
    border-radius: 2px;
    transition: width 0.3s ease;
    position: relative;
}

.progress-fill::after {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    animation: shimmer 2s infinite;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-4px); }
    75% { transform: translateX(4px); }
}

@keyframes shimmer {
    0% { transform: translateX(-100%); }
    100% { transform: translateX(100%); }
}

.bounce-in {
    animation: bounceIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

@media (max-width: 576px) {
    .mtcn-input-group {
        gap: 0.5rem;
    }
    
    .mtcn-input {
        width: 60px;
        height: 50px;
        font-size: 1.25rem;
    }

    .mtcn-input.wide {
        width: 80px;
    }

    .mtcn-container {
        padding: 1.5rem;
    }
}
</style>
@endpush

@push('scripts')
<script>
// Enhanced MTCN UX with Livewire integration
(function(){
    class LivewireMTCNInput {
        constructor() {
            this.inputs = [
                document.getElementById('mtcn1'),
                document.getElementById('mtcn2'),
                document.getElementById('mtcn3')
            ];
            this.maxLengths = [3, 3, 4];
            this.progressFill = document.getElementById('progressFill');
            this.statusElement = document.getElementById('mtcnStatus');
            this.statusText = document.getElementById('statusText');
            this.clearBtn = document.getElementById('clearBtn');
            this.pasteBtn = document.getElementById('pasteBtn');

            if (this.inputs.some(input => !input)) return;

            this.init();
        }

        init() {
            this.inputs.forEach((input, index) => {
                input.addEventListener('input', (e) => this.handleInput(e, index));
                input.addEventListener('keydown', (e) => this.handleKeyDown(e, index));
                input.addEventListener('focus', (e) => this.handleFocus(e));
                input.addEventListener('blur', (e) => this.handleBlur(e));
                input.addEventListener('paste', (e) => this.handlePaste(e, index));
            });

            this.clearBtn?.addEventListener('click', () => this.clearAll());
            this.pasteBtn?.addEventListener('click', () => this.pasteFromClipboard());

            // Initial update
            this.updateProgress();
            this.updateStatus();

            // Add bounce-in animation to container
            const container = document.querySelector('.mtcn-container');
            if (container) {
                container.classList.add('bounce-in');
            }
        }

        handleInput(e, index) {
            const input = e.target;
            let value = input.value.replace(/\D/g, '');
            
            // Limit to max length
            value = value.slice(0, this.maxLengths[index]);
            input.value = value;

            // Auto-advance to next field
            if (value.length === this.maxLengths[index] && index < this.inputs.length - 1) {
                this.inputs[index + 1].focus();
            }

            this.validateInput(input, index);
            this.updateProgress();
            this.updateStatus();
        }

        handleKeyDown(e, index) {
            const input = e.target;

            // Handle backspace
            if (e.key === 'Backspace' && input.value === '' && index > 0) {
                this.inputs[index - 1].focus();
                return;
            }

            // Handle arrow keys
            if (e.key === 'ArrowLeft' && index > 0) {
                e.preventDefault();
                this.inputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < this.inputs.length - 1) {
                e.preventDefault();
                this.inputs[index + 1].focus();
            }
        }

        handleFocus(e) {
            e.target.select();
        }

        handleBlur(e) {
            const input = e.target;
            const index = this.inputs.indexOf(input);
            this.validateInput(input, index);
        }

        handlePaste(e, index) {
            if (index === 0) {
                e.preventDefault();
                const pastedText = (e.clipboardData || window.clipboardData).getData('text');
                this.fillFromString(pastedText);
            }
        }

        validateInput(input, index) {
            const value = input.value;
            const required = this.maxLengths[index];

            // Remove Bootstrap validation classes but keep Livewire ones
            input.classList.remove('mtcn-valid', 'mtcn-invalid');

            if (value.length === required) {
                input.classList.add('mtcn-valid');
            } else if (value.length > 0) {
                input.classList.add('mtcn-invalid');
            }
        }

        updateProgress() {
            const totalChars = this.maxLengths.reduce((sum, max) => sum + max, 0);
            const currentChars = this.inputs.reduce((sum, input) => sum + input.value.length, 0);
            const progress = (currentChars / totalChars) * 100;
            
            if (this.progressFill) {
                this.progressFill.style.width = progress + '%';
            }
        }

        updateStatus() {
            if (!this.statusElement || !this.statusText) return;

            const isComplete = this.inputs.every((input, index) => 
                input.value.length === this.maxLengths[index]
            );

            if (isComplete) {
                this.statusElement.innerHTML = `
                    <div class="status-complete">
                        <i class="fas fa-check-circle"></i>
                        MTCN Complete: ${this.getMTCN()}
                    </div>
                `;
            } else {
                const remaining = this.maxLengths.reduce((sum, max, index) => 
                    sum + Math.max(0, max - this.inputs[index].value.length), 0
                );
                this.statusElement.innerHTML = `
                    <div class="status-incomplete">
                        ${remaining} {{ __('digits remaining') }}
                    </div>
                `;
            }
        }

        clearAll() {
            this.inputs.forEach(input => {
                input.value = '';
                input.classList.remove('mtcn-valid', 'mtcn-invalid');
                // Trigger Livewire update
                input.dispatchEvent(new Event('input'));
            });
            this.inputs[0].focus();
            this.updateProgress();
            this.updateStatus();
        }

        async pasteFromClipboard() {
            try {
                const text = await navigator.clipboard.readText();
                this.fillFromString(text);
            } catch (err) {
                // Fallback for browsers that don't support clipboard API
                const text = prompt('Paste your MTCN here:');
                if (text) {
                    this.fillFromString(text);
                }
            }
        }

        fillFromString(text) {
            const digits = text.replace(/\D/g, '');
            
            if (digits.length >= 10) {
                this.inputs[0].value = digits.slice(0, 3);
                this.inputs[1].value = digits.slice(3, 6);
                this.inputs[2].value = digits.slice(6, 10);

                // Trigger Livewire updates
                this.inputs.forEach((input, index) => {
                    this.validateInput(input, index);
                    input.dispatchEvent(new Event('input'));
                    
                    // Also trigger Livewire's wire:model update
                    if (window.Livewire) {
                        const component = input.closest('[wire\\:id]');
                        if (component) {
                            const componentId = component.getAttribute('wire:id');
                            const livewireComponent = window.Livewire.find(componentId);
                            if (livewireComponent) {
                                const fieldNames = ['mtcn1', 'mtcn2', 'mtcn3'];
                                livewireComponent.set(fieldNames[index], input.value);
                            }
                        }
                    }
                });

                this.inputs[2].focus();
                this.updateProgress();
                this.updateStatus();
            }
        }

        getMTCN() {
            return this.inputs.map(input => input.value).join('');
        }

        getFormattedMTCN() {
            return this.inputs.map(input => input.value).join('-');
        }
    }

    // Initialize when DOM is loaded or on Livewire updates
    function initMTCN() {
        new LivewireMTCNInput();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initMTCN);
    } else {
        initMTCN();
    }

    // Re-initialize on Livewire navigation
    document.addEventListener('livewire:navigated', initMTCN);
    document.addEventListener('livewire:load', initMTCN);
})();
</script>
@endpush