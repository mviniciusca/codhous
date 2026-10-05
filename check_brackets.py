with open('app/Filament/Resources/ContentSectionResource.php', 'r') as f:
    text = f.read()

def check(s):
    stack = []
    for i, c in enumerate(s):
        if c in '([{':
            stack.append((c, i))
        elif c in ')]}':
            if not stack:
                return f"Unmatched {c} at {i}"
            top, pos = stack.pop()
            if (top == '(' and c != ')') or (top == '[' and c != ']') or (top == '{' and c != '}'):
                return f"Mismatched {c} at {i}, expected matching for {top} from {pos}"
    if stack:
        return f"Unclosed {stack[-1][0]} at {stack[-1][1]}"
    return "OK"

print(check(text))
