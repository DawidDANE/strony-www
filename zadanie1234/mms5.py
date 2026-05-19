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

# usuwanie unnamed
df = df.loc[:, ~df.columns.str.contains("^Unnamed")]

# usuwanie spacji
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
# CZYSZCZENIE DANYCH LICZBOWYCH
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
# ZMIENNE OBJAŚNIAJĄCE
# ==========================================================

X = df[
    [
        "Research Environment",
        "Research Quality",
        "Industry Impact",
        "Teaching"
    ]
]

# ==========================================================
# MODEL REGRESJI WIELORAKIEJ
# ==========================================================

X_const = sm.add_constant(X)

model = sm.OLS(y, X_const).fit()

print("\nMODEL REGRESJI WIELORAKIEJ")
print("=" * 70)

print(model.summary())

# ==========================================================
# WSPÓŁCZYNNIK DETERMINACJI
# ==========================================================

r2 = model.rsquared

print(f"\nR^2 = {r2:.4f}")

print(
    f"\nModel wyjaśnia "
    f"{r2 * 100:.2f}% zmienności "
    f"Overall Score."
)

# ==========================================================
# PODZIAŁ DANYCH
# ==========================================================

X_train, X_test, y_train, y_test = train_test_split(
    X,
    y,
    test_size=0.2,
    random_state=42
)

# ==========================================================
# MODEL DO PROGNOZY
# ==========================================================

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

    "Name": df.loc[y_test.index, "Name"],

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

# usunięcie kolumny Roznica z wyświetlania
top10_final = top10[
    [
        "Name",
        "Rzeczywiste",
        "Prognozowane"
    ]
]

print("\n10 NAJLEPSZYCH PROGNOZ")
print("=" * 70)

print(top10_final)

# ==========================================================
# ZAPIS DO PLIKU TXT
# ==========================================================

with open(
    "model_regresji.txt",
    "w",
    encoding="utf-8"
) as f:

    f.write(
        "MODEL REGRESJI WIELORAKIEJ\n"
    )

    f.write("=" * 70 + "\n\n")

    f.write(str(model.summary()))

    f.write(f"\n\nR^2 = {r2:.4f}\n")

    f.write(
        f"\nModel wyjaśnia "
        f"{r2 * 100:.2f}% "
        f"zmienności Overall Score.\n"
    )

    f.write("\n")

    f.write(
        "10 NAJLEPSZYCH PROGNOZ\n"
    )

    f.write("=" * 70 + "\n\n")

    f.write(str(top10))

    f.write("\n\n")

    f.write("UŻYTE KOLUMNY:\n")

    f.write("\n")

    f.write(
        "Zmienna objaśniana:\n"
    )

    f.write(
        "- Overall Score\n"
    )

    f.write("\n")

    f.write(
        "Zmienne objaśniające:\n"
    )

    f.write(
        "- Research Environment\n"
    )

    f.write(
        "- Research Quality\n"
    )

    f.write(
        "- Industry Impact\n"
    )

    f.write(
        "- Teaching\n"
    )

print("\nZAPISANO PLIK:")
print("model_regresji.txt")