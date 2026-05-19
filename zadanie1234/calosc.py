import pandas as pd
import numpy as np
import statsmodels.api as sm
from scipy.stats import pearsonr
from sklearn.model_selection import train_test_split
from sklearn.preprocessing import PolynomialFeatures
from sklearn.linear_model import LinearRegression
import matplotlib.pyplot as plt
from io import StringIO

# ==========================================================
# WCZYTANIE DANYCH
# ==========================================================

plik = "Projekt_Koncowy_MMS.xlsx"

df = pd.read_excel(plik)

# ==========================================================
# CZYSZCZENIE KOLUMN
# ==========================================================

# usuwanie unnamed
df = df.loc[:, ~df.columns.str.contains("^Unnamed")]

# usuwanie spacji
df.columns = df.columns.str.strip()

# poprawa literówek
df.columns = df.columns.str.replace("ration", "Ratio")

print("KOLUMNY:")
print(df.columns.tolist())

# ==========================================================
# KOLUMNY LICZBOWE
# ==========================================================

numeric_cols = [
    "Overall Score",
    "Teaching",
    "Research Environment",
    "Research Quality",
    "Industry Impact"
]

# ==========================================================
# CZYSZCZENIE LICZB
# ==========================================================

for col in numeric_cols:

    if col in df.columns:

        df[col] = (
            df[col]
            .astype(str)
            .str.replace(" ", "", regex=False)
            .str.replace(",", ".", regex=False)
        )

        df[col] = pd.to_numeric(df[col], errors="coerce")

# ==========================================================
# USUWANIE BRAKÓW
# ==========================================================

df = df.dropna()

print("\nLICZBA WIERSZY:", len(df))

# ==========================================================
# ZMIENNA OBJAŚNIANA
# ==========================================================

y = df["Overall Score"]

# ==========================================================
# ZMIENNE OBJAŚNIAJĄCE
# ==========================================================

selected_columns = [
    "Research Environment",   # X1
    "Research Quality",       # X2
    "Industry Impact",        # X3
    "Teaching"                # X4
]

X = df[selected_columns]

# ==========================================================
# LISTA WYNIKÓW
# ==========================================================

wyniki = []

def dodaj(txt=""):
    wyniki.append(str(txt))

# ==========================================================
# ZADANIE 1
# ==========================================================

dodaj("=" * 70)
dodaj("ZADANIE 1 — WYBÓR DANYCH")
dodaj("=" * 70)

dodaj("""
Analiza danych rankingów uczelni.

Zmienna objaśniana:
Y = Overall Score

Zmienne objaśniające:
X1 = Research Environment
X2 = Research Quality
X3 = Industry Impact
X4 = Teaching
""")

# ==========================================================
# ZADANIE 2 — OPIS DANYCH
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 2 — OPIS DANYCH")
dodaj("=" * 70)

dodaj("\nPODGLĄD DANYCH:")
dodaj(df.head())

buffer = StringIO()
df.info(buf=buffer)

dodaj("\nINFO O DANYCH:")
dodaj(buffer.getvalue())

dodaj("\nSTATYSTYKI OPISOWE:")
dodaj(df.describe())

# ==========================================================
# ZADANIE 3 — WSPÓŁCZYNNIK ZMIENNOŚCI
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 3 — WSPÓŁCZYNNIKI ZMIENNOŚCI")
dodaj("=" * 70)

for col in X.columns:

    mean_val = df[col].mean()
    std_val = df[col].std()

    if mean_val != 0:

        cv = (std_val / mean_val) * 100

        dodaj(f"{col}: {cv:.2f}%")

# ==========================================================
# ZADANIE 4 — CEL BADANIA
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 4 — CEL BADANIA")
dodaj("=" * 70)

dodaj("""
Celem badania jest analiza wpływu:

- środowiska badawczego,
- jakości badań,
- wpływu przemysłowego,
- jakości nauczania

na końcowy wynik uczelni
(Overall Score).

Dodatkowo budowane są modele regresji
opisujące zależności pomiędzy zmiennymi.
""")

# ==========================================================
# ZADANIE 5 — REGRESJA WIELORAKA
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 5 — REGRESJA WIELORAKA")
dodaj("=" * 70)

X_const = sm.add_constant(X)

model = sm.OLS(y, X_const).fit()

dodaj(model.summary())

dodaj(f"\nR^2 = {model.rsquared:.6f}")

# ==========================================================
# PROGNOZA
# ==========================================================

X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.2,
    random_state=42
)

X_train_const = sm.add_constant(X_train)
X_test_const = sm.add_constant(X_test)

model_pred = sm.OLS(y_train, X_train_const).fit()

pred = model_pred.predict(X_test_const)

prognoza = pd.DataFrame({
    "Rzeczywiste": y_test.values,
    "Prognozowane": pred.values
})

dodaj("\nPROGNOZA:")
dodaj(prognoza.head(10))

# ==========================================================
# ZADANIE 6 — METODA PAWŁOWSKIEGO
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 6 — METODA PAWŁOWSKIEGO")
dodaj("=" * 70)

korelacje = {}

for col in X.columns:

    corr = abs(df[col].corr(y))
    korelacje[col] = corr

wybrane = sorted(
    korelacje,
    key=korelacje.get,
    reverse=True
)

dodaj("\nSiła korelacji zmiennych:")

for k, v in korelacje.items():

    dodaj(f"{k}: {v:.6f}")

dodaj("\nUszeregowanie zmiennych:")
dodaj(wybrane)

# ==========================================================
# ZADANIE 7 — ANALIZA KORELACJI
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 7 — ANALIZA KORELACJI")
dodaj("=" * 70)

istotne = []

for col in X.columns:

    corr, pval = pearsonr(df[col], y)

    if pval < 0.05:

        istotne.append(col)

        dodaj("\n" + "-" * 60)
        dodaj(f"Zmienna: {col}")
        dodaj(f"Korelacja Pearsona: {corr:.6f}")
        dodaj(f"P-value: {pval:.15f}")
        dodaj("Wniosek: korelacja ISTOTNA statystycznie")

# ==========================================================
# ISTOTNE ZMIENNE
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ISTOTNE ZMIENNE")
dodaj("=" * 70)

for x in istotne:

    dodaj(x)

# ==========================================================
# ZADANIE 8 — REGRESJA DLA ISTOTNYCH
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 8 — REGRESJA DLA ISTOTNYCH ZMIENNYCH")
dodaj("=" * 70)

X_sel = sm.add_constant(df[istotne])

model_sel = sm.OLS(y, X_sel).fit()

dodaj(model_sel.summary())

# ==========================================================
# ZADANIE 9 — ANALIZA SZCZEGÓŁOWA
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("ZADANIE 9 — ANALIZA SZCZEGÓŁOWA")
dodaj("=" * 70)

naj = sorted(
    korelacje,
    key=korelacje.get,
    reverse=True
)[:2]

for variable in naj:

    dodaj("\n")
    dodaj("#" * 60)
    dodaj(f"ZMIENNA: {variable}")
    dodaj("#" * 60)

    # ======================================================
    # REGRESJA LINIOWA
    # ======================================================

    dodaj("\nREGRESJA LINIOWA")

    X_lin = sm.add_constant(df[[variable]])

    linear_model = sm.OLS(y, X_lin).fit()

    dodaj(linear_model.summary())

    corr, pval = pearsonr(df[variable], y)

    dodaj(f"\nKorelacja: {corr:.6f}")
    dodaj(f"P-value: {pval:.15f}")

    # ======================================================
    # REGRESJA WIELOMIANOWA
    # ======================================================

    dodaj("\nREGRESJA WIELOMIANOWA STOPNIA 2")

    poly = PolynomialFeatures(degree=2)

    X_poly = poly.fit_transform(df[[variable]])

    poly_model = LinearRegression()

    poly_model.fit(X_poly, y)

    r2_poly = poly_model.score(X_poly, y)

    dodaj(f"R^2 wielomianowy = {r2_poly:.6f}")

    # ======================================================
    # PORÓWNANIE
    # ======================================================

    dodaj("\nPORÓWNANIE MODELI")

    r2_lin = linear_model.rsquared

    dodaj(f"R^2 liniowy = {r2_lin:.6f}")
    dodaj(f"R^2 wielomianowy = {r2_poly:.6f}")

    if r2_poly > r2_lin:

        dodaj("Lepszy model: WIELOMIANOWY")

    else:

        dodaj("Lepszy model: LINIOWY")

    # ======================================================
    # WYKRES
    # ======================================================

    plt.figure(figsize=(8, 5))

    plt.scatter(df[variable], y)

    x_line = np.linspace(
        df[variable].min(),
        df[variable].max(),
        100
    )

    params = linear_model.params.values

    y_line = params[0] + params[1] * x_line

    plt.plot(x_line, y_line)

    plt.xlabel(variable)
    plt.ylabel("Overall Score")

    plt.title(f"Regresja liniowa — {variable}")

    plt.savefig(f"{variable}_linear.png")

    plt.close()

# ==========================================================
# STATYSTYKI DLA ISTOTNYCH ZMIENNYCH
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("STATYSTYKI DLA ISTOTNYCH ZMIENNYCH")
dodaj("=" * 70)

for col in istotne:

    srednia = df[col].mean()

    odchylenie = df[col].std()

    wsp_zmiennosci = (odchylenie / srednia) * 100

    dodaj("\n" + "-" * 60)
    dodaj(f"Zmienna: {col}")
    dodaj(f"Średnia: {srednia:.6f}")
    dodaj(f"Odchylenie standardowe: {odchylenie:.6f}")
    dodaj(f"Współczynnik zmienności: {wsp_zmiennosci:.2f}%")

# ==========================================================
# WNIOSKI
# ==========================================================

dodaj("\n")
dodaj("=" * 70)
dodaj("WNIOSKI")
dodaj("=" * 70)

dodaj("""
1. Zmienną objaśnianą był Overall Score.

2. Analizowano wpływ:
   - Research Environment,
   - Research Quality,
   - Industry Impact,
   - Teaching.

3. Dla każdej zmiennej obliczono:
   - korelację Pearsona,
   - p-value,
   - istotność statystyczną.

4. Najsilniejszą zależność z Overall Score
   wykazała zmienna Research Quality.

5. Model regresji liniowej dobrze opisuje dane.
""")

# ==========================================================
# ZAPIS DO PLIKU
# ==========================================================

with open("korelacje_wyniki.txt", "w", encoding="utf-8") as f:

    for line in wyniki:

        f.write(str(line))
        f.write("\n")

print("\nZAPISANO:")
print("korelacje_wyniki.txt")

print("\nWygenerowano wykresy PNG.")