import pandas as pd
import numpy as np
import matplotlib.pyplot as plt

from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import PolynomialFeatures
from sklearn.metrics import r2_score

# ==========================================================
# DANE
# ==========================================================

df = pd.read_excel("Projekt_Koncowy_MMS.xlsx")

df = df.loc[:, ~df.columns.str.contains("^Unnamed")]
df.columns = df.columns.str.strip()

cols = ["Overall Score", "Research Quality", "Research Environment"]

for col in cols:
    df[col] = (
        df[col].astype(str)
        .str.replace(",", ".", regex=False)
        .str.replace(" ", "", regex=False)
    )
    df[col] = pd.to_numeric(df[col], errors="coerce")

df = df.dropna()

y = df["Overall Score"]

X1 = "Research Quality"
X2 = "Research Environment"

# ==========================================================
# MODEL LINIOWY
# ==========================================================

lin_model = LinearRegression()
lin_model.fit(df[[X1, X2]], y)

r2_lin = lin_model.score(df[[X1, X2]], y)

# ==========================================================
# MODEL LOGARYTMICZNY (POPRAWNY)
# ==========================================================

df_log = df[(df[X1] > 0) & (df[X2] > 0)].copy()

X_log = np.log(df_log[[X1, X2]])
y_log = df_log["Overall Score"]

log_model = LinearRegression()
log_model.fit(X_log, y_log)

r2_log = log_model.score(X_log, y_log)

# ==========================================================
# MODEL WIELOMIANOWY
# ==========================================================

poly = PolynomialFeatures(degree=2, include_bias=False)

X_poly = poly.fit_transform(df[[X1, X2]])

poly_model = LinearRegression()
poly_model.fit(X_poly, y)

r2_poly = poly_model.score(X_poly, y)

# ==========================================================
# WYBÓR MODELU
# ==========================================================

best = max([
    (r2_lin, "liniowy"),
    (r2_log, "logarytmiczny"),
    (r2_poly, "wielomianowy")
])

best_model = best[1]

# ==========================================================
# PROGNOZA
# ==========================================================

new_data = pd.DataFrame({
    X1: [80, 75, 70, 65, 85],
    X2: [75, 70, 68, 72, 78]
})

names = [
    "Kirikkale University",
    "Kocaeli University",
    "Perm Polytechnic",
    "Ionian University",
    "Burdur University"
]

if best_model == "liniowy":
    preds = lin_model.predict(new_data)

elif best_model == "logarytmiczny":
    preds = log_model.predict(np.log(new_data))

else:
    preds = poly_model.predict(poly.transform(new_data))

# ==========================================================
# ZAPIS WYNIKÓW
# ==========================================================

with open("wyniki.txt", "w", encoding="utf-8") as f:
    f.write(f"R2 liniowy: {r2_lin}\n")
    f.write(f"R2 logarytmiczny: {r2_log}\n")
    f.write(f"R2 wielomianowy: {r2_poly}\n")
    f.write(f"Najlepszy model: {best_model}\n\n")

    f.write("PROGNOZA:\n")
    for i in range(len(names)):
        f.write(f"{names[i]} -> {preds[i]}\n")

# ==========================================================
# 6 WYKRESÓW (POLSKIE NAZWY)
# ==========================================================

# 1. LINIOWY X1
plt.figure()
plt.scatter(df[X1], y, alpha=0.5)
m1 = LinearRegression().fit(df[[X1]], y)
plt.plot(df[X1], m1.predict(df[[X1]]), color="red")
plt.title("Model liniowy - Research Quality")
plt.savefig("wykres_liniowy_X1.png")
plt.close()

# 2. WIELOMIAN X1
plt.figure()
x_sorted = np.sort(df[X1])

poly1 = PolynomialFeatures(2)
X1p = poly1.fit_transform(df[[X1]])
mp1 = LinearRegression().fit(X1p, y)

plt.scatter(df[X1], y, alpha=0.5)
plt.plot(x_sorted, mp1.predict(poly1.transform(x_sorted.reshape(-1,1))), color="green")
plt.title("Model wielomianowy - Research Quality")
plt.savefig("wykres_wielomianowy_X1.png")
plt.close()

# 3. LINIOWY X2
plt.figure()
plt.scatter(df[X2], y, alpha=0.5)
m2 = LinearRegression().fit(df[[X2]], y)
plt.plot(df[X2], m2.predict(df[[X2]]), color="red")
plt.title("Model liniowy - Research Environment")
plt.savefig("wykres_liniowy_X2.png")
plt.close()

# 4. WIELOMIAN X2
plt.figure()
x_sorted = np.sort(df[X2])

poly2 = PolynomialFeatures(2)
X2p = poly2.fit_transform(df[[X2]])
mp2 = LinearRegression().fit(X2p, y)

plt.scatter(df[X2], y, alpha=0.5)
plt.plot(x_sorted, mp2.predict(poly2.transform(x_sorted.reshape(-1,1))), color="green")
plt.title("Model wielomianowy - Research Environment")
plt.savefig("wykres_wielomianowy_X2.png")
plt.close()

# 5. LOGARYTMICZNY (X1 + X2)
plt.figure()

df_log_plot = df[(df[X1] > 0) & (df[X2] > 0)]

log_model_plot = LinearRegression()
log_model_plot.fit(np.log(df_log_plot[[X1, X2]]), df_log_plot["Overall Score"])

x1_sorted = np.sort(df[X1])
x2_mean = np.mean(df[X2])

X_curve = pd.DataFrame({
    X1: x1_sorted,
    X2: x2_mean
})

y_curve = log_model_plot.predict(np.log(X_curve))

plt.scatter(df[X1], y, alpha=0.3)
plt.plot(x1_sorted, y_curve, color="purple", linewidth=2)
plt.title("Model logarytmiczny - X1 + X2")
plt.savefig("wykres_logarytmiczny.png")
plt.close()

# 6. ROZKŁAD DANYCH
plt.figure()
plt.scatter(df[X1], y, alpha=0.3)
plt.scatter(df[X2], y, alpha=0.3)
plt.title("Rozkład danych - X1 i X2")
plt.savefig("wykres_rozkladu.png")
plt.close()

# ==========================================================
# OUTPUT
# ==========================================================

for i in range(len(names)):
    print(names[i], "->", preds[i])

print("\nGOTOWE: 6 wykresów + wyniki.txt")