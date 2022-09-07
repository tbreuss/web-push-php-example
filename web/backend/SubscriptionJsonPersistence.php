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
        $subscriptions = $this->findAll();
        $subscriptions[] = $subscription;
        $this->overwriteAll($subscriptions);
    }
    
    public function remove(array $subscription): void
    {
        $subscriptions = $this->findAll();
        $key = array_search($subscription['endpoint'], array_column($subscriptions, 'endpoint'));
        if ($key !== false) {
            unset($subscriptions[$key]);
            $this->overwriteAll($subscriptions);
        }
    }
    
    public function update(array $subscription): void
    {
        $subscriptions = $this->findAll();
        $key = array_search($subscription['endpoint'], array_column($subscriptions, 'endpoint'));
        if ($key !== false) {
            $subscriptions[$key] = $subscription;
            $this->overwriteAll($subscriptions);
        }
    }
    
    public function findByEndpoint(string $endpoint): ?array
    {
        $subscriptions = $this->findAll();
        $key = array_search($endpoint, array_column($subscriptions, 'endpoint'));
        if ($key !== false) {
            return $subscriptions[$key];
        }      
        return null;
    }
    
    public function findAll(): array
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
