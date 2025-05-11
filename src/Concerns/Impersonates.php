<?php

namespace Esagod\FilamentImpersonate\Concerns;

use Closure;
use Filament\Facades\Filament;
use Illuminate\Http\RedirectResponse;
use Esagod\Impersonate\Services\ImpersonateManager;
use Livewire\Features\SupportRedirects\Redirector;

trait Impersonates
{
    protected Closure|string|null $guard = null;

    protected bool $openNewWindow = false;
    protected Closure|string|null $redirectTo = null;

    protected Closure|string|null $backTo = null;

    public static function getDefaultName(): ?string
    {
        return 'impersonate';
    }

    public function guard(Closure|string $guard): self
    {
        $this->guard = $guard;

        return $this;
    }

	public function openNewWindow(bool $openNewWindow): self
	{
		$this->openNewWindow = $openNewWindow;

		return $this;
	}

    public function redirectTo(Closure|string $redirectTo): self
    {
        $this->redirectTo = $redirectTo;

        return $this;
    }

    public function backTo(Closure|string $backTo): self
    {
        $this->backTo = $backTo;

        return $this;
    }

    public function getGuard(): string
    {
        return $this->evaluate($this->guard) ?? Filament::getCurrentPanel()->getAuthGuard();
    }

	public function getOpenNewWindow(): string
	{
		return $this->evaluate($this->openNewWindow) ?? config('filament-impersonate.open_new_window');
	}

    public function getRedirectTo(): string
    {
        return $this->evaluate($this->redirectTo) ?? config('filament-impersonate.redirect_to');
    }

    public function getBackTo(): ?string
    {
        return $this->evaluate($this->backTo);
    }

    protected function canBeImpersonated($target): bool
    {
        $current = Filament::auth()->user();

	    return $current->isNot($target)
		    && !($this->guard == Filament::getCurrentPanel()->getAuthGuard() && app(ImpersonateManager::class)->isImpersonating())
		    && (!method_exists($current, 'canImpersonate') || $current->canImpersonate())
		    && (!method_exists($target, 'canBeImpersonated') || $target->canBeImpersonated());
    }

    public function impersonate($record): bool|Redirector|RedirectResponse
    {
        if (!$this->canBeImpersonated($record)) {
            return false;
        }

        session()->put([
            'impersonate.back_to' => $this->getBackTo() ?? request('fingerprint.path', request()->header('referer')) ?? Filament::getCurrentPanel()->getUrl(),
            'impersonate.guard' => $this->getGuard()
        ]);

        app(ImpersonateManager::class)->take(
            Filament::auth()->user(),
            $record,
            $this->getGuard()
        );

		if ($this->getOpenNewWindow()) {
		    $livewire->js('window.open(\'' .$this->getRedirectTo() . '\', \'_blank\');');
	        return true;
	    }

	    return redirect($this->getRedirectTo());
    }
}
