#!/usr/bin/env python3
"""MCP server for Perplexity AI via Playwright browser automation"""

import json
import sys
import asyncio
from typing import Any
import re

try:
    from playwright.async_api import async_playwright, Browser, Page
except ImportError:
    print("Error: playwright not installed. Run: pip install playwright && playwright install", file=sys.stderr)
    sys.exit(1)


class PerplexityMCPServer:
    def __init__(self):
        self.browser: Browser | None = None
        self.page: Page | None = None
        self.playwright = None

    async def init_browser(self):
        """Initialize Playwright browser"""
        if self.browser is None:
            self.playwright = await async_playwright().start()
            self.browser = await self.playwright.chromium.launch(headless=False)
            self.page = await self.browser.new_page()
            # Navigate to Perplexity and wait for user to login (manually once)
            await self.page.goto("https://www.perplexity.ai/")
            await self.page.wait_for_url("https://www.perplexity.ai/")
            print("✅ Browser ready — login manually if needed", file=sys.stderr)

    async def close_browser(self):
        """Close browser"""
        if self.page:
            await self.page.close()
        if self.browser:
            await self.browser.close()
        if self.playwright:
            await self.playwright.stop()

    async def handle_request(self, request: dict) -> dict:
        """Handle MCP request"""
        method = request.get("method", "")
        params = request.get("params", {})

        if method == "tools/list":
            return self.list_tools()
        elif method == "tools/call":
            return await self.call_tool(params)
        elif method == "initialize":
            await self.init_browser()
            return {"protocolVersion": "2024-11-05", "capabilities": {}, "serverInfo": {"name": "perplexity-playwright-mcp", "version": "1.0.0"}}
        else:
            return {"error": f"Unknown method: {method}"}

    def list_tools(self) -> dict:
        """List available tools"""
        return {
            "tools": [
                {
                    "name": "perplexity_search",
                    "description": "Search Perplexity AI with a query using browser automation",
                    "inputSchema": {
                        "type": "object",
                        "properties": {
                            "query": {"type": "string", "description": "Search query"},
                        },
                        "required": ["query"],
                    },
                }
            ]
        }

    async def call_tool(self, params: dict) -> dict:
        """Call a tool"""
        tool_name = params.get("name", "")
        tool_input = params.get("input", {})

        if not self.page:
            await self.init_browser()

        try:
            if tool_name == "perplexity_search":
                query = tool_input.get("query", "")
                if not query:
                    return {"error": "Query is required"}

                result = await self.search_perplexity(query)
                return {
                    "content": [
                        {
                            "type": "text",
                            "text": result,
                        }
                    ]
                }
            else:
                return {"error": f"Unknown tool: {tool_name}"}
        except Exception as e:
            import traceback
            return {"error": f"Tool execution failed: {str(e)}\n{traceback.format_exc()}"}

    async def search_perplexity(self, query: str) -> str:
        """Search Perplexity and extract answer"""
        try:
            # Fill the search box
            await self.page.fill("[placeholder*='Ask anything']", query)
            await self.page.press("[placeholder*='Ask anything']", "Enter")

            # Wait for response to load
            await self.page.wait_for_selector("[data-testid='answer']", timeout=30000)

            # Extract answer text
            answer_element = await self.page.query_selector("[data-testid='answer']")
            if answer_element:
                answer_text = await answer_element.text_content()
                return answer_text.strip() if answer_text else "No answer received"
            else:
                return "Could not find answer element"
        except Exception as e:
            return f"Error searching Perplexity: {str(e)}"


async def main():
    server = PerplexityMCPServer()

    try:
        while True:
            try:
                line = sys.stdin.readline()
                if not line:
                    break

                request = json.loads(line)
                response = await server.handle_request(request)
                print(json.dumps(response), flush=True)
            except json.JSONDecodeError as e:
                print(json.dumps({"error": f"Invalid JSON: {e}"}), flush=True)
            except Exception as e:
                print(json.dumps({"error": str(e)}), flush=True)
    finally:
        await server.close_browser()


if __name__ == "__main__":
    asyncio.run(main())
