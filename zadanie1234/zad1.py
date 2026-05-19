import pandas as pd
import numpy as np
import statsmodels.api as sm

# =========================
# 1. DANE
# =========================

data = [
[102.60,1002.03,105.00,303.09],
[107.54,1043.24,108.05,317.70],
[110.76,1078.65,109.67,344.49],
[114.31,1182.77,113.17,348.37],
[119.68,1205.74,119.51,350.89],
[122.91,1220.89,122.02,343.90],
[125.12,1248.91,121.78,355.03],
[127.00,1344.87,123.61,347.70],
[130.94,1598.52,126.82,375.87],
[133.16,1659.48,129.61,396.05],
[135.16,1714.00,130.78,397.12],
[138.13,1856.31,134.96,417.72],
[142.04,1868.65,138.95,411.19],
[144.70,1869.78,142.75,437.76],
[146.73,1905.76,145.04,439.07],
[148.34,2051.74,147.50,450.34],
[149.83,2043.55,149.57,408.76],
[151.02,2006.92,152.26,403.82],
[151.78,2047.29,152.11,409.84],
[152.08,2152.99,153.02,409.39],
[152.84,2155.54,154.70,412.97],
[153.15,2061.95,155.32,408.62],
[153.45,2095.81,153.92,410.73],
[153.15,2225.41,154.54,407.95],
[152.08,2228.68,155.47,390.06],
[151.77,2141.01,156.09,386.73]
]

df = pd.DataFrame(data, columns=["Y","X1","X2","X3"])

# =========================
# 2. MODEL REGRESJI
# =========================

X = df[["X1","X2","X3"]]
X = sm.add_constant(X)
y = df["Y"]

model = sm.OLS(y, X).fit()

print("\n=== PODSUMOWANIE MODELU ===")
print(model.summary())

# =========================
# 3. PARAMETRY MODELU
# =========================

print("\n=== PARAMETRY MODELU ===")
print(f"beta0 (const): {model.params['const']}")
print(f"beta1 (X1): {model.params['X1']}")
print(f"beta2 (X2): {model.params['X2']}")
print(f"beta3 (X3): {model.params['X3']}")

# =========================
# 4. PROGNOZA (ZADANIE 2)
# =========================

X_new = np.array([[1, 2450, 172, 317.5]])
y_pred = model.predict(X_new)

print("\n=== PROGNOZA ===")
print(f"Prognozowana wartość Y: {y_pred[0]}")

# =========================
# 5. OCENA MODELU
# =========================

print("\n=== OCENA MODELU ===")
print(f"R^2: {model.rsquared}")
print(f"R^2 skorygowane: {model.rsquared_adj}")
print(f"Statystyka F: {model.fvalue}")
print(f"p-value (F): {model.f_pvalue}")

# =========================
# 6. WNIOSKI (do raportu)
# =========================

print("\n=== WNIOSKI ===")
if model.rsquared > 0.8:
    print("Model bardzo dobrze dopasowany.")
elif model.rsquared > 0.5:
    print("Model umiarkowanie dopasowany.")
else:
    print("Model słabo dopasowany.")

print("Sprawdź istotność parametrów (p-value < 0.05).")