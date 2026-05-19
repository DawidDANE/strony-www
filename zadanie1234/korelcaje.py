import pandas as pd
from scipy.stats import pearsonr

# =====================================
# Wczytanie pliku Excel
# =====================================
df = pd.read_excel("Projekt_Koncowy_MMS.xlsx")

# =====================================
# Czyszczenie nazw kolumn
# =====================================
df.columns = df.columns.str.strip()

# =====================================
# Overall Score = zmienna objaśniana
# =====================================
target = "Overall Score"

# Wszystkie zmienne objaśniające
features = [
    "Ranking",
    "Teaching",
    "Research Environment",
    "Research Quality",
    "Industry Impact",
    "International Outlook",
    "FTMRatio",
    "Students to Staff Ratio",
    "Student Population"
]

# =====================================
# Czyszczenie danych
# =====================================
all_cols = [target] + features

for col in all_cols:

    df[col] = (
        df[col]
        .astype(str)
        .str.replace(",", ".", regex=False)
        .str.replace(" ", "", regex=False)
    )

# Student Population ma separatory tysięcy
df["Student Population"] = (
    df["Student Population"]
    .str.replace(".", "", regex=False)
)

# Zamiana na liczby
for col in all_cols:
    df[col] = pd.to_numeric(df[col], errors="coerce")

# =====================================
# Liczenie korelacji
# =====================================
results = []

for feature in features:

    temp_df = df[[target, feature]].dropna()

    if len(temp_df) > 2:

        r, p = pearsonr(
            temp_df[target],
            temp_df[feature]
        )

        # Interpretacja istotności
        if p < 0.05:
            significance = "ISTOTNA STATYSTYCZNIE"
        else:
            significance = "NIEISTOTNA STATYSTYCZNIE"

        results.append({
            "Zmienna": feature,
            "Korelacja": round(r, 4),
            "p-value": round(p, 6),
            "Istotnosc": significance
        })

# =====================================
# Zapis wyników
# =====================================
with open("korelacje_wyniki.txt", "w", encoding="utf-8") as f:

    f.write("KORELACJE Z OVERALL SCORE\n")
    f.write("=" * 80 + "\n\n")

    for r in results:

        f.write(f"Zmienna objaśniająca: {r['Zmienna']}\n")
        f.write(f"Korelacja Pearsona r = {r['Korelacja']}\n")
        f.write(f"p-value = {r['p-value']}\n")
        f.write(f"Wynik: {r['Istotnosc']}\n")
        f.write("-" * 80 + "\n")

print("Gotowe. Wyniki zapisano do pliku: korelacje_wyniki.txt")