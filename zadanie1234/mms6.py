import pandas as pd
import statsmodels.api as sm
from sklearn.model_selection import train_test_split

# ==========================================================
# WCZYTANIE DANYCH
# ==========================================================

plik = "Projekt_Koncowy_MMS.xlsx"

df = pd.read_excel(plik)

# ==========================================================
# CZYSZCZENIE KOLUMN
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
# CZYSZCZENIE LICZB
# ==========================================================

for col in numeric_cols:

    if col in df.columns:

        df[col] = (
            df[col]
            .astype(str)
            .str.replace(",", ".", regex=False)
            .str.replace(" ", "", regex=False)
        )

        df[col] = pd.to_numeric(
            df[col],
            errors="coerce"
        )

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
# WSZYSTKIE ZMIENNE
# ==========================================================

all_variables = [
    "Research Environment",
    "Research Quality",
    "Industry Impact",
    "Teaching"
]

# ==========================================================
# METODA PAWŁOWSKIEGO
# k = 2
# ==========================================================

print("\nMETODA PAWŁOWSKIEGO")
print("=" * 70)

korelacje = {}

for col in all_variables:

    corr = abs(df[col].corr(y))

    korelacje[col] = corr

# sortowanie malejąco
sorted_variables = sorted(
    korelacje,
    key=korelacje.get,
    reverse=True
)

# wybór k=2
selected_variables = sorted_variables[:2]

print("\nWYBRANE ZMIENNE:")

for var in selected_variables:

    print(var)

# ==========================================================
# MODEL REGRESJI
# ==========================================================

X = df[selected_variables]

X_const = sm.add_constant(X)

model = sm.OLS(y, X_const).fit()

print("\nMODEL REGRESJI")
print("=" * 70)

print(model.summary())

# ==========================================================
# RÓWNANIE REGRESJI
# ==========================================================

params = model.params

print("\nRÓWNANIE REGRESJI")

equation = (
    f"Y = {params['const']:.4f}"
)

for var in selected_variables:

    equation += (
        f" + ({params[var]:.4f})*{var}"
    )

print(equation)

# ==========================================================
# WSPÓŁCZYNNIK DETERMINACJI
# ==========================================================

r2 = model.rsquared

print(f"\nR^2 = {r2:.4f}")

print(
    f"\nModel wyjaśnia "
    f"{r2*100:.2f}% "
    f"zmienności Overall Score."
)

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

model_pred = sm.OLS(
    y_train,
    X_train_const
).fit()

pred = model_pred.predict(X_test_const)

# ==========================================================
# TABELA PROGNOZ
# ==========================================================

wyniki = pd.DataFrame({

    "Name":
        df.loc[y_test.index, "Name"].values,

    "Rzeczywiste":
        y_test.values,

    "Prognozowane":
        pred.values
})

# ==========================================================
# BŁĄD PROGNOZY
# ==========================================================

wyniki["Roznica"] = abs(
    wyniki["Rzeczywiste"]
    -
    wyniki["Prognozowane"]
)

# ==========================================================
# 10 NAJLEPSZYCH PROGNOZ
# ==========================================================

top10 = wyniki.sort_values(
    by="Roznica"
).head(10)

# zostawiamy tylko potrzebne kolumny
top10_final = top10[
    [
        "Name",
        "Rzeczywiste",
        "Prognozowane"
    ]
]

print("\n10 NAJLEPSZYCH PROGNOZ")
print("=" * 70)

print(top10_final.to_string(index=False))

# ==========================================================
# ZAPIS DO PLIKU
# ==========================================================

with open(
    "pawlowski_model.txt",
    "w",
    encoding="utf-8"
) as f:

    f.write(
        "METODA PAWŁOWSKIEGO\n"
    )

    f.write("=" * 70 + "\n\n")

    f.write("Wybrane zmienne:\n")

    for var in selected_variables:

        f.write(f"- {var}\n")

    f.write("\n")

    f.write("MODEL REGRESJI\n")

    f.write("=" * 70 + "\n\n")

    f.write(str(model.summary()))

    f.write("\n\n")

    f.write("RÓWNANIE REGRESJI:\n")

    f.write(equation)

    f.write(f"\n\nR^2 = {r2:.4f}\n")

    f.write(
        f"\nModel wyjaśnia "
        f"{r2*100:.2f}% "
        f"zmienności Overall Score.\n"
    )

    f.write("\n\n")

    f.write("10 NAJLEPSZYCH PROGNOZ\n")

    f.write("=" * 70 + "\n\n")

    f.write(str(top10_final))

print("\nZAPISANO:")
print("pawlowski_model.txt")