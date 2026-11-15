class WalletData {
  final int availableMinor;
  final int heldMinor;
  final String currency;

  const WalletData({
    required this.availableMinor,
    required this.heldMinor,
    this.currency = 'PKR',
  });

  double get availablePkr => availableMinor / 100.0;
  double get heldPkr => heldMinor / 100.0;

  factory WalletData.fromJson(Map<String, dynamic> json) {
    return WalletData(
      availableMinor: (json['available_minor'] ?? 0) as int,
      heldMinor: (json['held_minor'] ?? 0) as int,
      currency: (json['currency'] ?? 'PKR') as String,
    );
  }
}

class SponsoredMission {
  final int id;
  final String title;
  final String description;
  final int rewardPkr;
  final String actionLabel;
  final String iconType;
  final bool isCompleted;

  const SponsoredMission({
    required this.id,
    required this.title,
    required this.description,
    required this.rewardPkr,
    required this.actionLabel,
    this.iconType = 'ad',
    this.isCompleted = false,
  });
}

class WithdrawalHistoryItem {
  final int id;
  final int amountMinor;
  final String method;
  final String status;
  final String date;

  const WithdrawalHistoryItem({
    required this.id,
    required this.amountMinor,
    required this.method,
    required this.status,
    required this.date,
  });

  double get amountPkr => amountMinor / 100.0;
}
