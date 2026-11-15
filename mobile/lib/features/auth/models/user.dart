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
  final String? avatarPath;

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
    this.avatarPath,
  });

  /// Unique formatted Account ID for user fetching & support
  String get uniqueCode => 'BLM-${id.toString().padLeft(5, '0')}';

  User copyWith({
    int? id,
    String? uuid,
    String? name,
    String? email,
    String? phoneE164,
    String? status,
    String? locale,
    String? preferredCurrency,
    List<String>? roles,
    String? avatarPath,
  }) {
    return User(
      id: id ?? this.id,
      uuid: uuid ?? this.uuid,
      name: name ?? this.name,
      email: email ?? this.email,
      phoneE164: phoneE164 ?? this.phoneE164,
      status: status ?? this.status,
      locale: locale ?? this.locale,
      preferredCurrency: preferredCurrency ?? this.preferredCurrency,
      roles: roles ?? this.roles,
      avatarPath: avatarPath ?? this.avatarPath,
    );
  }

  factory User.fromJson(Map<String, dynamic> json, {String? localAvatarPath}) {
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
      avatarPath: localAvatarPath ?? json['avatar_path']?.toString() ?? json['avatar_url']?.toString(),
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
      'avatar_path': avatarPath,
    };
  }
}
