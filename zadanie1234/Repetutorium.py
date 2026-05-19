import pandas as pd
import numpy as np
from scipy import stats
import matplotlib.pyplot as plt
import seaborn as sns

# =========================
# 1. WCZYTANIE DANYCH
# =========================
df = pd.read_csv("dane.txt", sep=r"\s+", header=None)

# =========================
# 2. PRZYGOTOWANIE DANYCH (TYLKO STYCZEŃ)
# =========================

# kolumna 1 = styczeń (bo 0 to rok)
df_january = df[[0, 1]].copy()

# polskie nazwy kolumn
df_january.columns = ["Data", "Temperatura"]

# =========================
# 3. STATYSTYKI OPISOWE
# =========================
print("\n--- STATYSTYKI OPISOWE (STYCZEŃ) ---")
print(df_january.describe())

# =========================
# 4. REGRESJA LINIOWA
# =========================
linear_regression = stats.linregress(
    df_january["Data"],
    df_january["Temperatura"]
)

print("\n--- REGRESJA LINIOWA ---")
print("Slope:", linear_regression.slope)
print("Intercept:", linear_regression.intercept)

# =========================
# 5. PROGNOZA NA 2026
# =========================
pred_2026 = linear_regression.slope * 2026 + linear_regression.intercept

print("\n--- PROGNOZA 2026 (STYCZEŃ) ---")
print("Temperatura:", pred_2026)

# =========================
# 6. BŁĄD PROGNOZY
# =========================
df_january["Pred"] = (
    linear_regression.slope * df_january["Data"] +
    linear_regression.intercept
)

df_january["Error"] = df_january["Temperatura"] - df_january["Pred"]

mae = np.mean(np.abs(df_january["Error"]))
rmse = np.sqrt(np.mean(df_january["Error"]**2))

print("\n--- BŁĄD MODELU ---")
print("MAE:", mae)
print("RMSE:", rmse)

# =========================
# 7. WIZUALIZACJA
# =========================
plt.figure(figsize=(12,6))

sns.scatterplot(
    data=df_january,
    x="Data",
    y="Temperatura",
    label="Dane (styczeń)"
)

plt.plot(
    df_january["Data"],
    df_january["Pred"],
    color="red",
    label="Regresja liniowa"
)

plt.scatter(
    2026,
    pred_2026,
    color="green",
    s=120,
    label="Prognoza 2026"
)

plt.title("Regresja liniowa temperatury – STYCZEŃ")
plt.xlabel("Rok")
plt.ylabel("Temperatura (styczeń)")
plt.legend()
plt.grid()

plt.show()