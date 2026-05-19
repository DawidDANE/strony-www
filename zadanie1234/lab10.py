import sympy as s
s.init_printing()
t = s.symbols('t', positive=True)
print("#" * 45)
print("PRZYKŁAD A")
print("całka: (cos(3t) - cos(4t)) / t na [0, ∞)")
print("#" * 45)
exprA = (s.cos(3*t) - s.cos(4*t)) / t
resultA = s.integrate(exprA, (t, 0, s.oo))
print("Postać symboliczna:")
s.pprint(resultA)
print("\nPrzybliżenie:")
print("{:.10f}".format(s.N(resultA)))
print("\n" + "#" * 45)
print("PRZYKŁAD B")
print("całka: sin(t) / (t² + t + 1) na [1, ∞)")
print("#" * 45)

exprB = s.sin(t) / (t**2 + t + 1)
resultB = s.integrate(exprB, (t, 1, s.oo))
print("Postać symboliczna:")
if resultB.has(s.Integral):
    print("wynik nie ma prostej formy analitycznej")
else:
    s.pprint(resultB)
print("\nPrzybliżenie:")
print("{:.10f}".format(s.N(resultB)))