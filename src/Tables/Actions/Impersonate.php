<?php
namespace Esagod\FilamentImpersonate\Tables\Actions;

use Closure;
use Esagod\FilamentImpersonate\Concerns\Impersonates;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;
use Livewire\Component;

class Impersonate extends Action
{
	use Impersonates;
	protected Model | Closure | null $_record = null;
	protected function setUp(): void
	{
		parent::setUp();

		$this
			->label(__('filament-impersonate::action.label'))
			->iconButton()
			->icon('impersonate-icon')
			->action(function($record, Component $livewire) {
				if ($record->impersonate) {
					if(is_a($record->impersonate, 'Illuminate\Database\Eloquent\Collection')) {
						if ($record->impersonate->count()) {
							return $this->impersonate($record->impersonate[0], $livewire);
						}
					} else {
						return $this->impersonate($record->impersonate, $livewire);
					}
				} else {
					return $this->impersonate($record, $livewire);
				}
			})
			->hidden(function($record) {
				if ($record->impersonate) {
					if (is_a($record->impersonate, 'Illuminate\Database\Eloquent\Collection')) {
						if ($record->impersonate->count()) {
							return !$this->canBeImpersonated($record->impersonate[0]);
						}
						return true;
					} else {
						return !$this->canBeImpersonated($record->impersonate);
					}
				} else {
					return !$this->canBeImpersonated($record);
				}
			});
	}
}
