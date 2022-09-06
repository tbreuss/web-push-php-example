<?php

class SubscriptionJsonPersistence
{
    private string $path;
    
    public function __construct(string $path)
    {
        $this->path = $path;
        $this->createStore();
    }
    
    public function add(array $subscription): void
    {
        $subscriptions = $this->fetchAll();
        $subscriptions[] = $subscription;
        $this->overwriteAll($subscriptions);
    }
    
    public function remove(array $subscription): void
    {
        $subscriptions = $this->fetchAll();
        $key = array_search($subscription['authToken'], array_column($subscriptions, 'authToken'));
        if ($key !== false) {
            unset($subscriptions[$key]);
            $this->overwriteAll($subscriptions);
        }
    }
    
    public function update(array $subscription): void
    {
        $subscriptions = $this->fetchAll();
        $key = array_search($subscription['authToken'], array_column($subscriptions, 'authToken'));
        if ($key !== false) {
            $subscriptions[$key] = $subscription;
            $this->overwriteAll($subscriptions);
        }
    }
    
    public function fetchAll(): array
    {
        return json_decode(file_get_contents($this->path), true);
    }
    
    private function overwriteAll(array $subscriptions): void
    {
        file_put_contents($this->path, json_encode(array_values($subscriptions), JSON_PRETTY_PRINT));
    }
    
    private function createStore(): void
    {
        if (!file_exists($this->path)) {
            file_put_contents($this->path, json_encode([]));
        }
    }
}
