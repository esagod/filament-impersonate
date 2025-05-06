<?php
namespace Esagod\FilamentImpersonate\Tables\Actions;

use Closure;
use Esagod\FilamentImpersonate\Concerns\Impersonates;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Model;

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
			->action(function($record) {
				if ($record->impersonate) {
					if(is_a($record->impersonate, 'Illuminate\Database\Eloquent\Collection')) {
						if ($record->impersonate->count()) {
							return $this->impersonate($record->impersonate[0]);
						}
					} else {
						return $this->impersonate($record->impersonate);
					}
				} else {
					return $this->impersonate($record);
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
