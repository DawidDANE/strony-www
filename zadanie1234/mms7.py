import pandas as pd
import seaborn as sns
import matplotlib.pyplot as plt
from scipy.stats import pearsonr
import statsmodels.api as sm

# ==========================================================
# WCZYTANIE DANYCH
# ==========================================================

plik = "Projekt_Koncowy_MMS.xlsx"
df = pd.read_excel(plik)

# ==========================================================
# CZYSZCZENIE
# ==========================================================

df = df.loc[:, ~df.columns.str.contains("^Unnamed")]
df.columns = df.columns.str.strip()

print("KOLUMNY:")
print(df.columns.tolist())

# ==========================================================
# KOLUMNY LICZBOWE
# ==========================================================

numeric_cols = [
    "Overall Score",
    "Research Environment",
    "Research Quality",
    "Industry Impact",
    "Teaching"
]

# ==========================================================
# KONWERSJA NA LICZBY
# ==========================================================

for col in numeric_cols:
    df[col] = (
        df[col]
        .astype(str)
        .str.replace(",", ".", regex=False)
        .str.replace(" ", "", regex=False)
    )
    df[col] = pd.to_numeric(df[col], errors="coerce")

# ==========================================================
# USUWANIE BRAKÓW
# ==========================================================

df = df.dropna()

print("\nLICZBA WIERSZY:")
print(len(df))

# ==========================================================
# ZMIENNA ZALEŻNA
# ==========================================================

y = df["Overall Score"]

# ==========================================================
# ZMIENNE OBJAŚNIAJĄCE
# ==========================================================

variables = [
    "Research Environment",
    "Research Quality",
    "Industry Impact",
    "Teaching"
]

# ==========================================================
# KORELACJA
# ==========================================================

print("\nKORELACJA")
print("=" * 70)

istotne = []
wyniki = []

wyniki.append("=== KORELACJA ===")

for col in variables:

    corr, pval = pearsonr(df[col], y)

    if pval < 0.05:
        istotne.append(col)

        if abs(corr) >= 0.7:
            sila = "silna"
        elif abs(corr) >= 0.3:
            sila = "umiarkowana"
        else:
            sila = "slaba"

        tekst = f"{col}: r={corr:.4f}, p={pval:.6f}, {sila} istotna"
        print(tekst)
        wyniki.append(tekst)

# ==========================================================
# MACIERZ KORELACJI
# ==========================================================

corr_matrix = df[variables + ["Overall Score"]].corr()

plt.figure(figsize=(8, 6))
sns.heatmap(corr_matrix, annot=True, cmap="coolwarm", fmt=".2f")
plt.title("Macierz korelacji")
plt.tight_layout()
plt.savefig("macierz_korelacji.png")
plt.close()

# ==========================================================
# REGRESJA LINIOWA
# ==========================================================

X = df[istotne]
X = sm.add_constant(X)

model = sm.OLS(y, X).fit()

print("\nMODEL REGRESJI")
print(model.summary())

# ==========================================================
# R²
# ==========================================================

r2 = model.rsquared
r2_adj = model.rsquared_adj

print("\nR²:", r2)
print("R² skorygowane:", r2_adj)

# ==========================================================
# RÓWNANIE MODELU
# ==========================================================

coef = model.params

equation = "Overall Score = "
equation += f"{coef['const']:.4f} "

for var in istotne:
    equation += f"+ ({coef[var]:.4f} * {var}) "

print("\nRÓWNANIE MODELU:")
print(equation)

# ==========================================================
# PROGNOZA
# ==========================================================

new_data = pd.DataFrame({
    "const": [1],
    "Research Environment": [75],
    "Research Quality": [80],
    "Industry Impact": [70]
})

new_data = new_data[["const"] + istotne]

prediction = model.predict(new_data)

print("\nPROGNOZA Overall Score:")
print(prediction.values[0])

# ==========================================================
# ZAPIS DO PLIKU TXT
# ==========================================================

with open("analiza_korelacji_i_regresji.txt", "w", encoding="utf-8") as f:

    f.write("=== KORELACJA ===\n")
    for line in wyniki:
        f.write(str(line) + "\n")

    f.write("\n=== MODEL REGRESJI ===\n")
    f.write(str(model.summary()))

    f.write("\n\nR²: " + str(r2))
    f.write("\nR² skorygowane: " + str(r2_adj))

    f.write("\n\n=== RÓWNANIE MODELU ===\n")
    f.write(equation)

    f.write("\n\n=== PROGNOZA ===\n")
    f.write(f"Prognozowany Overall Score: {prediction.values[0]:.2f}\n")

print("\nZAPISANO: analiza_korelacji_i_regresji.txt")
print("WYGNEROWANO: macierz_korelacji.png")