<?php

namespace ZRouter;

interface Middleware
{
    public function handle(): bool;
    public function callback(ZRouter $zrouter);
}
