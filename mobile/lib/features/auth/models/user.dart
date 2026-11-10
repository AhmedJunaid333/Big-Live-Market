class User {
  final int id;
  final String uuid;
  final String name;
  final String? email;
  final String? phoneE164;
  final String status;
  final String locale;
  final String preferredCurrency;
  final List<String> roles;

  User({
    required this.id,
    required this.uuid,
    required this.name,
    this.email,
    this.phoneE164,
    required this.status,
    required this.locale,
    required this.preferredCurrency,
    this.roles = const [],
  });

  factory User.fromJson(Map<String, dynamic> json) {
    final rolesList = (json['roles'] as List<dynamic>?)
            ?.map((r) => r is Map<String, dynamic> ? (r['name']?.toString() ?? '') : r.toString())
            .toList() ??
        [];

    return User(
      id: json['id'] as int,
      uuid: json['uuid']?.toString() ?? '',
      name: json['name']?.toString() ?? '',
      email: json['email']?.toString(),
      phoneE164: json['phone_e164']?.toString(),
      status: json['status']?.toString() ?? 'active',
      locale: json['locale']?.toString() ?? 'en',
      preferredCurrency: json['preferred_currency']?.toString() ?? 'PKR',
      roles: rolesList,
    );
  }

  Map<String, dynamic> toJson() {
    return {
      'id': id,
      'uuid': uuid,
      'name': name,
      'email': email,
      'phone_e164': phoneE164,
      'status': status,
      'locale': locale,
      'preferred_currency': preferredCurrency,
      'roles': roles,
    };
  }
}
