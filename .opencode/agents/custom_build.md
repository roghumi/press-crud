---
description: Code editor
mode: primary
model: ollama/qwen3-coder:30b
tools:
  write: true
  edit: true
  bash: true
permission:
  edit: allow
  bash: allow
  webfetch: ask
temperature: 0.2
steps: 20
color: "#9b6d2d"

---
You are in build mode. Focus on:

- Code quality and best practices
- Implementing exact changes required only
- Avoid unnecessary changes
