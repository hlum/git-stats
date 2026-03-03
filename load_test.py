#!/usr/bin/env python3
"""
Load testing script for GitHub Stats PHP API
Usage: python load_test.py [total_requests] [concurrent_workers]
"""

import asyncio
import aiohttp
import time
import sys
from dataclasses import dataclass
from typing import List

@dataclass
class Result:
    status: int
    duration: float
    success: bool
    error: str = ""

async def make_request(session: aiohttp.ClientSession, url: str) -> Result:
    start = time.perf_counter()
    try:
        async with session.get(url, timeout=aiohttp.ClientTimeout(total=30)) as response:
            await response.read()
            duration = time.perf_counter() - start
            return Result(
                status=response.status,
                duration=duration,
                success=response.status == 200
            )
    except Exception as e:
        duration = time.perf_counter() - start
        return Result(status=0, duration=duration, success=False, error=str(e))

async def run_load_test(url: str, total_requests: int, concurrency: int) -> List[Result]:
    connector = aiohttp.TCPConnector(limit=concurrency)
    async with aiohttp.ClientSession(connector=connector) as session:
        tasks = [make_request(session, url) for _ in range(total_requests)]
        results = await asyncio.gather(*tasks)
    return results

def print_results(results: List[Result], total_time: float):
    successful = [r for r in results if r.success]
    failed = [r for r in results if not r.success]
    
    print("\n" + "="*50)
    print("LOAD TEST RESULTS")
    print("="*50)
    print(f"Total Requests:    {len(results)}")
    print(f"Successful:        {len(successful)} ({100*len(successful)/len(results):.1f}%)")
    print(f"Failed:            {len(failed)} ({100*len(failed)/len(results):.1f}%)")
    print(f"Total Time:        {total_time:.2f}s")
    print(f"Requests/sec:      {len(results)/total_time:.2f}")
    
    if successful:
        durations = [r.duration for r in successful]
        print(f"\nResponse Times (successful):")
        print(f"  Min:     {min(durations)*1000:.0f}ms")
        print(f"  Max:     {max(durations)*1000:.0f}ms")
        print(f"  Avg:     {sum(durations)/len(durations)*1000:.0f}ms")
        sorted_d = sorted(durations)
        p50 = sorted_d[len(sorted_d)//2]
        p95 = sorted_d[int(len(sorted_d)*0.95)]
        p99 = sorted_d[int(len(sorted_d)*0.99)] if len(sorted_d) > 100 else sorted_d[-1]
        print(f"  P50:     {p50*1000:.0f}ms")
        print(f"  P95:     {p95*1000:.0f}ms")
        print(f"  P99:     {p99*1000:.0f}ms")
    
    if failed:
        print(f"\nErrors:")
        error_counts = {}
        for r in failed:
            key = r.error or f"HTTP {r.status}"
            error_counts[key] = error_counts.get(key, 0) + 1
        for error, count in sorted(error_counts.items(), key=lambda x: -x[1])[:5]:
            print(f"  {error}: {count}")

def main():
    # Configuration
    base_url = "https://24cm0138.main.jp/php/public/index.php"
    endpoint = "/api/stats?username=octocat"
    url = base_url + endpoint
    
    total_requests = int(sys.argv[1]) if len(sys.argv) > 1 else 50
    concurrency = int(sys.argv[2]) if len(sys.argv) > 2 else 10
    
    print(f"Load Testing: {url}")
    print(f"Total Requests: {total_requests}")
    print(f"Concurrency: {concurrency}")
    print("Starting...")
    
    start = time.perf_counter()
    results = asyncio.run(run_load_test(url, total_requests, concurrency))
    total_time = time.perf_counter() - start
    
    print_results(results, total_time)

if __name__ == "__main__":
    main()
