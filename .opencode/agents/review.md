---
description: Code review without changes
mode: primary
model: ollama/ukjin/Qwen3-30B-A3B-Thinking-2507-Deepseek-v3.1-Distill:4b
tools:
  write: false
  edit: false
  bash: true
permission:
  edit: deny
  bash:
    "*": ask
    "git status *": allow
    "git diff": allow
    "git log*": allow
    "grep *": allow
  webfetch: allow
temperature: 0.5
steps: 20
color: "#16cade"
textVerbosity: high
reasoningEffort: high
---
You are in build mode. Focus on:

- Code quality and best practices
- Implementing exact changes required only
- Avoid unnecessary changes
- Security considerations
