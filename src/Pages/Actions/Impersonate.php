<?php

namespace Esagod\FilamentImpersonate\Pages\Actions;

use Filament\Pages\Actions\Action;
use Esagod\FilamentImpersonate\Concerns\Impersonates;
use Livewire\Component;

class Impersonate extends Action
{
	use Impersonates;

	protected function setUp(): void
	{
		parent::setUp();

		$this
			->label(__('filament-impersonate::action.label'))
			->icon('impersonate-icon')
			->action(fn ($record, Component $livewire) => $this->impersonate($record, $livewire))
			->hidden(fn ($record) => !$this->canBeImpersonated($record));
	}
}
