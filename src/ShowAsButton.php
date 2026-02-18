<?php

namespace Opscale\NovaToolbarActions;

trait ShowAsButton
{
    public function showAsButton(bool $show = true): static
    {
        return $this->withMeta(['showAsButton' => $show]);
    }

    public function showOnIndexToolbar(bool $show = true): static
    {
        return $this->withMeta(['showOnIndexToolbar' => $show]);
    }

    public function showOnDetailToolbar(bool $show = true): static
    {
        return $this->withMeta(['showOnDetailToolbar' => $show]);
    }
}
